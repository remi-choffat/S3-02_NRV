<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;

class Authz
{
    /**
     * @throws AuthnException
     */
    public static function checkRole(int $expectedRole): void
    {
        $utilisateur = AuthProvider::getSignedInUser();
        if ($utilisateur->getRole() < $expectedRole) {
            throw new AuthnException("Accès refusé : privilèges insuffisants.");
        }
    }
}