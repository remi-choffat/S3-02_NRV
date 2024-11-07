<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use DateTime;

class Spectacle
{
    private int $id;
    private string $titre;
    private array $artistes;
    private array $images;
    private string $url;
    private DateTime $date;
    private string $horaire;
    private int $duree;
    private string $description;
    private int $nb_places;

    public function __construct(int $id, string $titre, DateTime $date, string $horaire, int $duree, array $artistes, int $nb_places, string $description)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->artistes = $artistes;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->duree = $duree;
        $this->description = $description;
        $this->nb_places = $nb_places;
    }

    /**
     * @param Soiree $s
     * @return string génére une description du spectacle
     */
    public function genererDescription(Soiree $s): string
    {
        $res = "Le $this->titre sera réalisé par les artistes: ";
        for ($i = 0; $i < sizeof($this->artistes) - 2; $i++) {
            $res .= $this->artistes[$i] . ", ";
        }
        $date = $s->getDate();
        $res .= "et " . $this->artistes[sizeof($this->artistes) - 1] . " aura lieu le $date à $this->horaire";
        return $res;
    }

    /**
     * @return string
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * @return array
     */
    public function getartistes(): array
    {
        return $this->artistes;
    }

    /**
     * @return string
     */
    public function getHoraire(): string
    {
        return $this->horaire;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Helper method to implode artistes array
     * @return string
     */
    private function implodeArtistes(): string
    {
        if (empty($this->artistes)) {
            return "<i>Inconnu</i>";
        } else {
            return implode(", ", $this->artistes);
        }
    }

    /**
     * Rendu HTML de l'objet
     * @return string
     */
    public function afficherResume(): string
    {
        return <<<HTML
                <div class="box">
                    <h3 class="title is-4"><a href="?action=details-spectacle&id={$this->id}">{$this->titre}</a></h3>
                    <p><b>Artistes :</b> {$this->implodeArtistes()}</p>
                    <p><b>Date :</b> {$this->date->format('d/m/Y')}</p>
                    <p><b>Heure :</b> $this->horaire</p>
                </div>
        HTML;
    }

    /**
     * Rendu HTML détaillé de l'objet
     * @return string
     */
    public function afficherDetails(): string
    {
        return <<<HTML
                <div class="box">
                    <h3 class="title is-3">{$this->titre}</h3>
                    <p><b>Artistes :</b> {$this->implodeArtistes()}</p>
                    <p><b>Date :</b> {$this->date->format('d/m/Y')}</p>
                    <p><b>Heure :</b> $this->horaire</p>
                    <p><b>Durée :</b> $this->duree minutes</p>
                    <p><b>Nombre de places :</b> $this->nb_places</p>
                    <p><b>Description :</b> $this->description</p>
                </div>
        HTML;
    }

}