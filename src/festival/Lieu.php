<?php
declare(strict_types=1);
namespace iutnc\nrv\festival;
class Lieu{

    private string $nom;
    private string $addresse;
    private array $image;
    private int $nb_places_assises;
    private int $nb_places_debout;

    /**
     * @param string $nom
     * @param string $addresse
     * @param int $nb_places_assises
     * @param int $nb_places_debout
     *crée un Lieu
     */

    public function __construct(string $nom,string $addresse,int $nb_places_assises,int $nb_places_debout)
    {
        $this->image= [];
        $this->nom=$nom;
        $this->addresse=$addresse;
        $this->nb_places_debout = $nb_places_debout;
        $this->nb_places_assises=$nb_places_assises;
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
    public function addImage(string $lien){
        $this->image[] = $lien;
    }

    /**
     * @return string
     */
    public function getAddresse(): string
    {
        return $this->addresse;
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
    public function equals(Lieu $l):bool{
        return $this->nom === $l->getNom() && $this->addresse ===$l->getAddresse();
    }
}