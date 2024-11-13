<?php

namespace iutnc\nrv\action;

class DefaultAction extends Action
{
    /**
     * Affiche un message de bienvenue
     * @return string
     */
    public function execute(): string
    {
        // Si un utilisateur est connecté, on affiche son nom
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $message = "Bienvenue " . $utilisateur->getNom() . " !";
        } else {
            // Sinon, on affiche un message de bienvenue par défaut
            $message = "Bienvenue sur le site du festival NRV !";
        }

        return "<h2 class='subtitle'>$message</h2>";
    }
}