<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use iutnc\nrv\repository\NRVRepository;

/**
 * Représente un lieu
 */
class Lieu
{

    private ?int $id;
    private string $nom;
    private string $adresse;
    private array $images;
    private int $nb_places_assises;
    private int $nb_places_debout;

    /**
     * Constructeur de Lieu
     * @param ?int $id
     * @param string $nom
     * @param string $adresse
     * @param int $nb_places_assises
     * @param int $nb_places_debout
     * Crée un Lieu
     */
    public function __construct(?int $id, string $nom, string $adresse, int $nb_places_assises, int $nb_places_debout)
    {
        $this->id = $id ?? -1;
        $this->nom = $nom;
        $this->adresse = $adresse;
        $this->nb_places_debout = $nb_places_debout;
        $this->nb_places_assises = $nb_places_assises;
        if ($id !== null) {
            $this->images = NRVRepository::getInstance()->getImagesLieu($this->id);
        } else {
            $this->images = [];
        }
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
     * Renvoie, sous forme de tableau, le code HTML des images du lieu
     * @return array
     */
    public function getImagesHTML(): array
    {
        $imagesHTML = [];
        foreach ($this->images as $image) {
            $imagesHTML[] = "<img src='resources/images/{$image}' alt='Image du lieu {$this->nom}' class='lieu-image'>";
        }
        return $imagesHTML;
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
        return $this->nom . " (" . $this->adresse . ") - " . $this->nb_places_assises . " places assises, " . $this->nb_places_debout . " places debout";
    }


    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    public function getGoogleMapsEmbedUrl(): string
    {
        $nom = urlencode($this->nom);
        $address = urlencode($this->adresse);
        return "https://www.google.com/maps?q=$nom+$address&output=embed";
    }


    /**
     * Affiche la page de détails d'un lieu
     * @return string
     */
    public function afficherDetails(): string
    {
        if ($this->images) {
            $imagesHTML = "<div class='images-container'>" . implode('', $this->getImagesHTML()) . "</div>";
        } else {
            $imagesHTML = "";
        }

        $googleMapsEmbedUrl = $this->getGoogleMapsEmbedUrl();

        return <<<HTML
        <div class="box">
            <h3 class="title is-3">{$this->getNom()}</h3>
            <p><b>Adresse : </b>{$this->getAdresse()}</p>
            <p><b>Nombre de places : </b> {$this->getNbPlacesAssises()} assises, {$this->getNbPlacesDebout()} debout</p>
            $imagesHTML
            <iframe
                width="600"
                height="450"
                style="border:0"
                loading="lazy"
                allowfullscreen
                src="{$googleMapsEmbedUrl}">
            </iframe>
        </div>
HTML;
    }

}