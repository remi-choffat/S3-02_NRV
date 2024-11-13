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
    private int $role;
    private string $password;


    /**
     * Constructeur de la classe
     * @param int|null $id
     * @param string $nom
     * @param string $email
     * @param string $password
     * @param int $role
     */
    public function __construct(?int $id, string $nom, string $email, string $password, int $role)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }


    /**
     * Définit l'ID de l'utilisateur
     * @param int $id ID de l'utilisateur
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    /**
     * Renvoie l'ID de l'utilisateur
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