<?php

namespace iutnc\nrv\action;

use iutnc\nrv\action\Action;
use iutnc\nrv\auth\AuthProvider;

/**
 * Gère la déconnexion
 */
class Deconnexion extends Action
{

    /**
     * @return string
     */
    public function execute(): string
    {
        AuthProvider::SignedOutUser();
        return "<div class='notification is-success'>Vous avez été déconnecté</div>";
    }
}