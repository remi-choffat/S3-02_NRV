<?php
declare(strict_types=1);

namespace iutnc\nrv\action;

use Exception;
use iutnc\nrv\action\Action;
use iutnc\nrv\auth\AuthProvider;

/**
 * Action de Connexion d'un utilisateur
 */
class Connexion extends Action
{

    /**
     * Exécute l'action en fonction de la méthode HTTP
     * @return string
     */
    public function execute(): string
    {

        $result = "";
        return match ($this->http_method) {
            'GET' => $this->getConnexion(),
            'POST' => $this->postConnexion(),
            default => '',
        };
    }


    /**
     * Renvoie le formulaire d'inscription
     * @return string
     */
    private function getConnexion(): string
    {
        return <<<HTML
    <section class="section">
        <h1 class="title">Connexion</h1>
        <form action="index.php?action=connexion" method="POST">
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
            <br/>
            <div class="field">
                <div class="control">
                    <button class="button is-link">Connexion</button>
                </div>
            </div>
        </form>
</section>
HTML;
    }


    /**
     * Traite le formulaire de connexion
     * et connecte l'utilisateur si possible
     */
    private function postConnexion(): string
    {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        try {
            AuthProvider::signin($email, $password);
            $utilisateur = AuthProvider::getSignedInUser();
            // Renvoie l'utilisateur vers la page d'accueil
            header("Location: index.php");
        } catch (Exception $e) {
            return "<div class='notification is-danger'>{$e->getMessage()}</div>";
        }
        return "";
    }
}