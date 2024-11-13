<?php

namespace iutnc\nrv\auth;

use Exception;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\User\Utilisateur;
use PDOException;

class AuthProvider
{

    public static function signin($email, $password): void
    {
        $repo = NRVRepository::getInstance();
        $user = $repo->getUtilisateur($email);

        if (!$user || !password_verify($password, $user->getPassword())) {
            throw new PDOException("Identifiants invalides.");
        }
        // L'authentification a réussi
        $_SESSION["utilisateur"] = serialize($user);
    }

    /**
     * @throws AuthnException
     */
    public static function register(Utilisateur $utilisateur): void
    {
        if (preg_match('/@[A-z]+\.[A-z]+$/', $utilisateur->getEmail()) === 0) {
            throw new AuthnException("l'email saisi est invalide");
        }
        $password = $utilisateur->getPassword();
        $error = "Le mot de passe ne comporte pas: ";
        if (strlen($password) < 12) {
            $error .= "au moins 12 caractères.<br>";
        }
        if (!preg_match("/\d/", $password)) {
            $error .= "au moins 1 chiffre.<br>";
        }
        if (!preg_match("/[\W_]/", $password)) {
            $error .= "au moins 1 caractère spéciale.<br>";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $error .= "au moins une majuscule.<br>";
        }
        if (str_contains($error, "au moins")) {
            throw new AuthnException($error);
        }else {
            $repo = NRVRepository::getInstance();
            $repo->addUtilisateur($utilisateur);
        }
    }

    /**
     * @throws AuthnException
     */
    public static function getSignedInUser(): Utilisateur
    {
        if (!isset($_SESSION['utilisateur'])) {
            throw new AuthnException("Aucun utilisateur authentifié.");
        }
        return unserialize($_SESSION['utilisateur']);
    }
}