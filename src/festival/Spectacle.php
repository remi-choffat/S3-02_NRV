<?php
namespace iutnc\nrv\festival;

class Spectacle
{
    private string $titre;
    private array $artistes;
    private array $images;
    private string $url;
    private string $horaire;
    private Lieu $lieu;

    public function __construct(string $titre, array $artistes, string $horaire, Lieu $lieu)
    {
        $this->images = [];
        $this->artistes = $artistes;
        $this->titre = $titre;
        $this->url = "";
        $this->horaire = $horaire;
        $this->lieu = $lieu;
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

    public function getLieu(): Lieu
    {
        return $this->lieu;
    }

}