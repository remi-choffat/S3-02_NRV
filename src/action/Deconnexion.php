<?php
namespace iutnc\nrv\action;
use iutnc\nrv\action\Action;
use iutnc\nrv\auth\AuthProvider;
/**
 * bouton pour se déconncter
 */
class Deconnexion extends Action {
        /**
     * Exécute l'action en fonction de la méthode HTTP
     * @return string
     */
    public function execute(): string
    {
        AuthProvider::SignedOutUser();
        return "<p>Vous êtes déconnecté</p>";
    }
}