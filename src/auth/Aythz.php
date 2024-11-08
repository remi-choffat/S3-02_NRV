<?php

namespace iutnc\nrv\auth;

use iutnc\nrv\exception\AuthnException;

class Aythz
{
    public static function checkRole(int $expectedRole)
    {
        $user = AuthProvider::getSignedInUser();
        if ($user->getRank() < $expectedRole) {
            throw new AuthnException("Accès refusé : privilèges insuffisants.");
        }
    }
}