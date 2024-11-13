<?php

namespace iutnc\nrv\auth;

use Exception;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\exception\InscriptionException;
use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\User\Utilisateur;
use PDOException;

/**
 * Gère l'authentification
 */
class AuthProvider
{

    /**
     * Authentifie un utilisateur
     * @throws AuthnException
     */
    public static function signin($email, $password): void
    {
        $repo = NRVRepository::getInstance();
        $idPassd = $repo->getIdPasswd($email);

        if (!password_verify($password, $idPassd['password'])) {
            throw new PDOException("Identifiants invalides.");
        }
        // L'authentification a réussi
        $_SESSION["utilisateur"] = serialize($repo->getUtilisateur($idPassd['id']));
    }


    /**
     * Enregistre un utilisateur
     * @throws AuthnException|InscriptionException
     */
    public static function register(Utilisateur $utilisateur, string $password): void
    {
        if (preg_match('/@[A-z]+\.[A-z]+$/', $utilisateur->getEmail()) === 0) {
            throw new AuthnException("L'email saisi est invalide");
        }
        $error = "Le mot de passe ne comporte pas : <br/>";
        if (strlen($password) < 12) {
            $error .= "- au moins 12 caractères<br>";
        }
        if (!preg_match("/\d/", $password)) {
            $error .= "- au moins 1 chiffre<br>";
        }
        if (!preg_match("/[\W_]/", $password)) {
            $error .= "- au moins 1 caractère spécial<br>";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $error .= "- au moins une majuscule<br>";
        }
        if (str_contains($error, "au moins")) {
            throw new AuthnException($error);
        } else {
            $repo = NRVRepository::getInstance();
            $repo->addUtilisateur($utilisateur, password_hash($password, PASSWORD_BCRYPT));
        }
    }


    /**
     * Récupère l'utilisateur authentifié
     * @throws AuthnException
     */
    public static function getSignedInUser(): Utilisateur
    {
        if (!isset($_SESSION['utilisateur'])) {
            throw new AuthnException("Aucun utilisateur authentifié.");
        }
        return unserialize($_SESSION['utilisateur']);
    }


    /**
     * Déconnecte l'utilisateur
     * @return bool true si l'utilisateur était connecté, false sinon
     */
    public static function SignedOutUser(): bool
    {
        $result = false;
        if (isset($_SESSION['utilisateur'])) {
            unset($_SESSION['utilisateur']);
            $result = true;
        }
        return $result;
    }
}