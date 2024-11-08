<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AccessControlException;

class Authz
{
    /**
     * @throws AccessControlException 
     */
    public static function checkRole(int $expectedRole): void
    {
        $utilisateur = AuthProvider::getSignedInUser();
        if ($utilisateur->getRole() < $expectedRole) {
            throw new AccessControlException ("Accès refusé : privilèges insuffisants.");
        }
    }
}