<?php

namespace iutnc\nrv\auth;

use Exception;
use iutnc\nrv\exception\AuthnException;
use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\User\Utilisateur;
use PDOException;

class AuthProvider
{

    public static function signin($email, $password)
    {
        try {
            $db = NRVRepository::getInstance();
            $query = "SELECT password FROM UTILISATEUR WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['passwd'])) {
                throw new PDOException("Identifiants invalides.");
            }
            // L'authentification a réussi
            $_SESSION["user"] = new Utilisateur($db->getUserRank($email), $email);
        } catch (PDOException $e) {
            throw new AuthnException("Erreur lors de la connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function register($email, $password)
    {
        if (preg_match('/@[A-z]+\.[A-z]+$/', $email) === 0) {
            throw new AuthnException("l'email saisi est invalide");
        }
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
        }

        $db = NRVRepository::getInstance();

        // Vérification si l'utilisateur existe déjà
        $query = "SELECT id FROM UTILISATEUR WHERE email = :email";
        $stmt = $db->prepare($query);
        try {
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch()) {
                throw new AuthnException("Un utilisateur avec cet email existe déjà.");
            }

            // Hachage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            // Insertion de l'utilisateur dans la base de données
            $insertQuery = "INSERT INTO UTILISATEUR (email, password, role) VALUES (:email, :passwd, 1)";
            $stmt = $db->prepare($insertQuery);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':passwd', $hashedPassword);
            $stmt->execute();

        } catch (Exception $e) {
            throw new PDOException("DataBase access error, création de compte impossible impossible");
        }

    }

    public static function getSignedInUser(): Utilisateur
    {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Aucun utilisateur authentifié.");
        }
        return $_SESSION['user'];
    }
}