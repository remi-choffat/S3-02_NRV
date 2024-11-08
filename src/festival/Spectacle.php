<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use DateTime;

/**
 * Représente un spectacle
 */
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
    private Lieu $lieu;
    private bool $annule;
    private ?int $soireeId;
    private string $style;


    /**
     * Constructeur de la classe Spectacle
     * @param int $id
     * @param string $titre
     * @param DateTime $date
     * @param int $duree
     * @param array $artistes
     * @param string $style
     * @param Lieu $lieu
     * @param string $description
     * @param bool $annule
     * @param int|null $soireeId
     */
    public function __construct(int $id, string $titre, DateTime $date, int $duree, array $artistes, string $style, Lieu $lieu, string $description, bool $annule = false, int $soireeId = null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->artistes = $artistes;
        $this->date = $date;
        $this->horaire = $date->format('H:i');
        $this->duree = $duree;
        $this->description = $description;
        $this->lieu = $lieu;
        $this->annule = $annule;
        $this->soireeId = $soireeId;
        $this->style = $style;
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
    public function getArtistes(): array
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
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }


    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }


    /**
     * Renvoie le style du spectacle
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * @return int l'ID de la soirée
     */
    public function getSoireeId(): int
    {
        return $this->soireeId;
    }


    /**
     * Renvoie le lieu du spectacle
     */
    public function getLieu(): Lieu
    {
        return $this->lieu;
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
     * @return int
     */
    public function getDuree(): int
    {
        return $this->duree;
    }


    /**
     * @return DateTime retourne la date avec l'heure de fin dans une chaîne sous la forme yyyy-mm-dd hh:ii:ss
     */
    public function getFin(): DateTime
    {
        $d = $this->date;
        $temps = $this->getDuree();
        return $d->add(\DateInterval::createFromDateString("$temps minutes"));
    }


    /**
     * Rendu HTML de l'objet (résumé)
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

        // Vérifie si un spectacle est en cours (débuté mais pas terminé)
        $debut = new DateTime($this->date->format('Y-m-d') . ' ' . $this->horaire);
        $fin = (clone $debut)->modify("+{$this->duree} minutes");
        $enCours = $debut < new DateTime() && $fin > new DateTime();

        $statut = "<span class='tag is-info'>A venir</span>";
        if ($this->annule) {
            $statut = "<span class='tag is-danger'>Annulé</span>";
        } else if ($enCours) {
            $statut = "<span class='tag is-success'>En cours</span>";
        } else if ($this->date < new DateTime()) {
            $statut = "<span class='tag is-warning'>Passé</span>";
        } else if ($this->date < new DateTime('+1 day')) {
            $statut = "<span class='tag is-info'>Aujourd'hui</span>";
        } else if ($this->date < new DateTime('+2 day')) {
            $statut = "<span class='tag is-info'>Demain</span>";
        }

        return <<<HTML
                <div class="box">
                    <h3 class="title is-3">{$this->titre}</h3>
                    <p>$statut</p><br/>
                    <p><b>Artistes :</b> {$this->implodeArtistes()}</p>
                    <p><b>Style :</b> $this->style</p>
                    <p><b>Date :</b> {$this->date->format('d/m/Y')}</p>
                    <p><b>Heure :</b> $this->horaire</p>
                    <p><b>Durée :</b> $this->duree minutes</p>
                    <p><b>Lieu :</b> {$this->lieu->getNom()} ({$this->lieu->getAdresse()})</p>
                    <p><b>Nombre de places :</b> {$this->lieu->getNbPlacesAssises()} assises, {$this->lieu->getNbPlacesDebout()} debout</p>
                    <p><b>Description :</b> $this->description</p>
                </div>
        HTML;
    }

}