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
    private string $nom;
    private string $email;
    private int $role;


    /**
     * Constructeur de la classe
     * @param int|null $id
     * @param string $nom
     * @param string $email
     * @param string $password
     * @param int $role
     */
    public function __construct(string $nom, string $email, int $role)
    {
        $this->nom = $nom;
        $this->email = $email;
        $this->role = $role;
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
     * Renvoie le rôle de l'utilisateur
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

}