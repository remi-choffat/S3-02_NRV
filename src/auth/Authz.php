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
        $user = AuthProvider::getSignedInUser();
        if ($user->getRank() < $expectedRole) {
            throw new AuthnException("Accès refusé : privilèges insuffisants.");
        }
    }
}