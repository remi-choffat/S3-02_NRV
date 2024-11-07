<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;
class Lieu
{

    private int $id;
    private string $nom;
    private string $adresse;
    private array $image;
    private int $nb_places_assises;
    private int $nb_places_debout;

    /**
     * @param int $id
     * @param string $nom
     * @param string $adresse
     * @param int $nb_places_assises
     * @param int $nb_places_debout
     * Crée un Lieu
     */

    public function __construct(int $id, string $nom, string $adresse, int $nb_places_assises, int $nb_places_debout)
    {
        $this->id = $id;
        $this->image = [];
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->nb_places_debout = $nb_places_debout;
        $this->nb_places_assises = $nb_places_assises;
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
     * @param string $lien
     * @return void
     */
    public function addImage(string $lien)
    {
        $this->image[] = $lien;
    }

    /**
     * @return string
     */
    public function getAdresse(): string
    {
        return $this->adresse;
    }

    /**
     * @return int
     */
    public function getNbPlacesDebout(): int
    {
        return $this->nb_places_debout;
    }

    /**
     * @return int
     */
    public function getNbPlacesAssises(): int
    {
        return $this->nb_places_assises;
    }

    /**
     * @return array les images du lieu
     */
    public function getImage(): array
    {
        return $this->image;
    }

    /**
     * @param Lieu $l
     * @return bool vrai si les deux Lieux sont identiques
     */
    public function equals(Lieu $l): bool
    {
        return $this->nom === $l->getNom() && $this->adresse === $l->getAdresse();

    }

    /**
     * toString
     */
    public function __toString(): string
    {
        return $this->nom;
    }

}