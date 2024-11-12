<?php

namespace iutnc\nrv\User;
/**
 * Classe représentant un utilisateur
 */
class Utilisateur
{
    /**
     * attributs de la classe
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
     */
    public function __construct(string $nom, string $email, string $password, int $role, ?int $id){
        $this->nom = $nom;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id;
    }
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return string 
     */
    public function getNom(): string
    {
        return $this->nom;
    }
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }
}