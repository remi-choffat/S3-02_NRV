<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AccessControlException;
use iutnc\nrv\exception\AuthnException;

class Authz
{
    /**
     * Vérifie si l'utilisateur peut faire une action
     * @throws AccessControlException|AuthnException
     */
    public static function checkRole(int $expectedRole): void
    {
        $utilisateur = AuthProvider::getSignedInUser();
        if ($utilisateur->getRole() > $expectedRole) {
            throw new AccessControlException ("Accès refusé : privilèges insuffisants.");
        }
    }
}