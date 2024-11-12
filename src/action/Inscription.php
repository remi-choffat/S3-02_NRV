<?php
declare(strict_types=1);
namespace iutnc\nrv\action;

use Exception;
use iutnc\nrv\action\Action;
use iutnc\nrv\auth\AuthProvider;
use iutnc\nrv\User\Utilisateur;

/**
 * action inscription
 */
class Inscription extends Action {
    /**
     * function execute : execute la requête
     * en fonction du type de la requête soit get ou post
     * @return void
     */
    public function execute():string {
       
        $result="";
        $result = match ($this->http_method) {
            'GET' => $this->getInscription(),
            'POST' => $this->postInscription(),
            default => '',
        };
        return $result;
    }
    /**
     * getInscription : retourne le formulaire d'inscription
     * @return string
     */
    private function getInscription():string {
        return <<<HTML
        <section class="section">
        <div class="container">
            <h1 class="title">Inscription</h1>
            <form action="index.php?action=inscription" method="post">
                <div class="field">
                    <label class="label">Nom</label>
                    <div class="control">
                        <input class="input" type="text" name="nom" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Email</label>
                    <div class="control">
                        <input class="input" type="email" name="email" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Mot de passe</label>
                    <div class="control">
                        <input class="input" type="password" name="password" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="role">Role</label>
                    <div class="control">
                        <select id="role" name="role" class="input" required>
                            <option value="0">Administrateur</option>
                            <option value="1" selected>Staff</option>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-link">S'inscrire</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    HTML;
    }
    /**
     * methode postInscription : traite le formulaire d'inscription
     * et enregistre l'utilisateur si possible
     */
    private function postInscription():string {
        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS);
        $role = filter_var($_POST['role'], FILTER_SANITIZE_SPECIAL_CHARS);
        $role = (int)filter_var($_POST['role'], FILTER_SANITIZE_NUMBER_INT);
        $retour = "";
        try{
            $utlisateurs = new Utilisateur($nom, $email, $password, $role, null);
            AuthProvider::register($utlisateurs);
            $retour = "Inscription réussie";
        }  catch(Exception $e){
            $retour = $e->getMessage();
        }
        return $retour;
    }
}