<?php

namespace iutnc\nrv\User;

/**
 * Représente un utilisateur
 */
class Utilisateur
{

    /**
     * Attributs de la classe
     */
    private ?int $id;
    private string $nom;
    private string $email;
    private string $password;
    private int $role;

    /**
     * Constructeur de la classe
     * @param string $nom
     * @param string $email
     * @param string $password
     * @param int $role
     * @param int|null $id
     */
    public function __construct(string $nom, string $email, string $password, int $role, ?int $id)
    {
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id;
    }

    /**
     * Renvoie l'identifiant de l'utilisateur
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Renvoie le nom de l'utilisateur
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * Renvoie l'email de l'utilisateur
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Renvoie le mot de passe de l'utilisateur
     * @return string
     */
    // TODO - Doit-on vraiment stocker le mot de passe ?
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Renvoie le rôle de l'utilisateur
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

}