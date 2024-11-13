<?php
declare(strict_types=1);

namespace iutnc\nrv\action;

use Exception;
use iutnc\nrv\action\Action;
use iutnc\nrv\auth\AuthProvider;
use iutnc\nrv\User\Utilisateur;

/**
 * Action d'inscription d'un utilisateur
 */
class Inscription extends Action
{

    /**
     * Exécute l'action en fonction de la méthode HTTP
     * @return string
     */
    public function execute(): string
    {

        $result = "";
        return match ($this->http_method) {
            'GET' => $this->getInscription(),
            'POST' => $this->postInscription(),
            default => '',
        };
    }


    /**
     * Renvoie le formulaire d'inscription
     * @return string
     */
    private function getInscription(): string
    {
        return <<<HTML
    <section class="section">
        <h1 class="title">Inscrire un utilisateur</h1>
        <form action="index.php?action=inscription" method="POST">
            <div class="field">
                <label class="label required" for="nom">Nom</label>
                <div class="control">
                    <input class="input" id="nom" type="text" name="nom" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="email">Email</label>
                <div class="control">
                    <input class="input" id="email" type="email" name="email" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="password">Mot de passe</label>
                <div class="control">
                    <input class="input" id="password" type="password" name="password" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="confirm_password">Confirmer le mot de passe</label>
                <div class="control">
                    <input class="input" id="confirm_password" type="password" name="confirm_password" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="role">Role</label>
                <div class="control">
                    <select id="role" name="role" class="input" required>
                        <option value="1" selected>Staff</option>
                        <option value="0">Administrateur</option>
                    </select>
                </div>
            </div>
            <br/>
            <div class="field">
                <div class="control">
                    <button class="button is-link">S'inscrire</button>
                </div>
            </div>
        </form>
</section>
HTML;
    }


    /**
     * Traite le formulaire d'inscription
     * et enregistre l'utilisateur si possible
     */
    private function postInscription(): string
    {
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        $confirm_password = filter_var($_POST['confirm_password'], FILTER_SANITIZE_SPECIAL_CHARS);
        $role = (int)filter_var($_POST['role'], FILTER_SANITIZE_NUMBER_INT);
        $retour = "";

        if ($password !== $confirm_password) {
            return "<section class='section'><strong>Les mots de passe ne correspondent pas.</strong></section>";
        }

        try {
            $utilisateur = new Utilisateur($nom, $email, $password, $role, null);
            AuthProvider::register($utilisateur);
            $retour = "<section class='section'><strong>Utilisateur enregistré ✅</strong></section>";
        } catch (Exception $e) {
            $retour = "<section class='section'><strong>{$e->getMessage()}</strong></section>";
        }

        return $retour;
    }

}