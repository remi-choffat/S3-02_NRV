<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use DateMalformedStringException;
use DateTime;
use iutnc\nrv\exception\DateIncompatibleException;
use iutnc\nrv\repository\NRVRepository;


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
     * @param int|null $id
     * @param string $titre
     * @param DateTime $date
     * @param int $duree
     * @param array $artistes
     * @param string $style
     * @param Lieu $lieu
     * @param string $description
     * @param bool $annule
     * @param int|null $soireeId
     * @throws DateIncompatibleException
     */
    public function __construct(?int $id, string $titre, DateTime $date, int $duree, array $artistes, string $style, Lieu $lieu, string $description, bool $annule, int $soireeId = null)
    {

        // Vérifie la cohérence entre la date et le lieu du spectacle et la date et le lieu de la soirée,
        // si le spectacle appartient à une soirée
        // if ($soireeId !== null) {
        //      $soiree = NRVRepository::getInstance()->soireeAssignable($soireeId,$date,$lieu,$style);
        //      if ($soiree->getDate() > $date || $soiree->getDate()->format('Y-m-d') !== $date->format('Y-m-d')) {
        //       throw new DateIncompatibleException();
        //    }
        //   if ($soiree->getLieu()->getId() !== $lieu->getId()) {
        //      throw new LieuIncompatibleException();
        //   }
        //   }
        $this->date = $date;
        $this->duree = $duree;
        $this->annule = $annule;
        $this->id = $id ?? -1;
        $this->titre = $titre;
        $this->description = $description;
        $this->horaire = $date->format('H:i');
        if ($soireeId !== null) {
            $this->verifierDatePourSoiree();

        }
        $this->artistes = $artistes;
        $this->lieu = $lieu;
        $this->soireeId = $soireeId;
        $this->style = $style;
        if ($id !== null) {
            $this->images = NRVRepository::getInstance()->getImagesSpectacle($this->id);
        } else {
            $this->images = [];
        }
    }


    /**
     * Renvoie, sous forme de tableau, le code HTML des images du spectacle
     * @return array
     */
    public function getImagesHTML(): array
    {
        $imagesHTML = [];
        foreach ($this->images as $image) {
            $imagesHTML[] = "<img src='resources/images/{$image}' alt='Image du spectacle {$this->titre}' class='spectacle-image'>";
        }
        return $imagesHTML;
    }


    /**
     * Renvoie le code HTML de la première image du spectacle
     * @return string
     */
    public function getFirstImageHTML(): string
    {
        if (empty($this->images)) {
            return "";
        } else {
            return "<img src='resources/images/{$this->images[0]}' alt='Image du spectacle {$this->titre}' class='spectacle-image-resume'>";
        }
    }


    /**
     * @param int $id l'ID du spectacle
     * @return void
     */
    public function setId(int $id): void
    {
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
     * Renvoie le style du spectacle
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }


    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url ?? null;
    }


    /**
     * @return int|null l'ID de la soirée, null si le spectacle n'appartient pas à une soirée
     */
    public function getSoireeId(): ?int
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
     * @return void
     * @throws DateIncompatibleException renvoie une erreur si la date du spectacle n'est pas correcte pour être dans une soiree
     */
    public function verifierDatePourSoiree(): void
    {
        if (((int)$this->date->format("H") > 5 && (int)$this->date->format("H") < 17)
            || ((int)$this->date->format("H") < 5 && $this->date->format("H:i") != "00:00")) {
            throw new DateIncompatibleException("L'horaire de début choisit pour le spectacle doit être entre 17h et 5h");
        } else {
            $datefin = $this->getFin();
            if (((int)$datefin->format("H") > 5 && (int)$datefin->format("H") < 17)
                || ((int)$datefin->format("H") < 5 && $datefin->format("H:i") != "00:00")) {
                throw new DateIncompatibleException("La durée de spectacle est trop longue, le spectacle doit finir au plus tard à 5h");
            }
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
     * Indique si le spectacle est annulé
     * @return bool true si le spectacle est annulé, false sinon
     */
    public function isAnnule(): bool
    {
        return $this->annule;
    }


    /**
     * Annule ou restaure un spectacle
     * @param bool $annule true pour annuler le spectacle, false pour le restaurer
     * @return void
     */
    public function setAnnule(bool $annule): void
    {
        $this->annule = $annule;
    }


    /**
     * Renvoie la description du spectacle
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


    /**
     * Affiche la durée du spectacle en heures et minutes
     * @return string
     */
    public function afficherDuree(): string
    {
        if ($this->duree < 60) {
            return $this->duree . " minutes";
        } elseif ($this->duree % 60 == 0) {
            return ($this->duree / 60) . " heure" . ($this->duree / 60 > 1 ? "s" : "");
        } else {
            return floor($this->duree / 60) . " heure" . (floor($this->duree / 60) > 1 ? "s" : "") . " et " . ($this->duree % 60) . " minutes";
        }
    }


    /**
     * Renvoie la date formatée en français
     * @param bool $afficherHeure true pour afficher l'heure, false sinon
     * @return string
     */
    public function getFormattedDate(bool $afficherHeure = true): string
    {
        $months = [
            'January' => 'janvier', 'February' => 'février', 'March' => 'mars', 'April' => 'avril',
            'May' => 'mai', 'June' => 'juin', 'July' => 'juillet', 'August' => 'août',
            'September' => 'septembre', 'October' => 'octobre', 'November' => 'novembre', 'December' => 'décembre'
        ];
        $formattedDate = $this->date->format('d F Y');
        return str_replace(array_keys($months), array_values($months), $formattedDate) . ($afficherHeure ? " à " . $this->horaire : "");
    }


    /**
     * Rendu HTML de l'objet (résumé)
     * @return string
     */
    public function afficherResume(): string
    {
        // Affiche le menu de modification et d'annulation si l'utilisateur est connecté
        if (isset($_SESSION['utilisateur'])) {
            $cancelAction = $this->isAnnule() ? 'restaurer-spectacle' : 'annuler-spectacle';
            $cancelMessage = $this->isAnnule() ? 'Restaurer' : 'Annuler';
            $menu = <<<HTML
    <div class="menu">
        <button class="menu-btn">⋮</button>
        <div class="menu-content">
            <a href="index.php?action=modifier-spectacle&id={$this->id}">Modifier</a>
            <a href="index.php?action={$cancelAction}&id={$this->id}">$cancelMessage</a>
        </div>
    </div>
HTML;
        } else {
            $menu = "";
        }

        $annuleTag = $this->isAnnule() ? "<p><span class='tag is-danger'>Annulé</span></p>" : "";
        $starClass = in_array($this->id, $_SESSION["favoris"] ?? []) ? 'filled' : 'empty';

        // Retrieve the first image
        $firstImageHTML = "";
        if (!empty($this->images)) {
            $firstImageHTML = $this->getFirstImageHTML();
        }

        return <<<HTML
<div class="box">
    <div class="spectacle-header">
        <h3 class="title is-4"><a href="?action=details-spectacle&id={$this->id}">{$this->titre}</a></h3>
        <div class="actions-container">
            <span class="star $starClass" data-id="{$this->id}"></span>
            $menu
        </div>
    </div>
    $annuleTag
    <div class="spectacle-content">
        <div class="spectacle-text">
            <p><b>Artistes :</b> {$this->implodeArtistes()}</p>
            <p><b>Date :</b> {$this->getFormattedDate(true)}</p>
        </div>
        $firstImageHTML
    </div>
</div>
HTML;
    }


    /**
     * Rendu HTML de l'objet (résumé compact)
     * @param string $type Type de résumé compact
     * @return string
     */
    public function afficherResumeCompact(string $type): string
    {
        return <<<HTML
                <div class="box is-one-third">
                    <h3 class="title is-5"><a href="?action=details-spectacle&id={$this->id}">{$this->titre}</a></h3>
                    <p><b><span class="fa fa-arrow-circle-o-right"></span> $type</b></p>
                    <p>{$this->getFormattedDate(true)}</p>
                </div>
        HTML;
    }


    /**
     * Rendu HTML détaillé de l'objet
     * @return string
     * @throws DateMalformedStringException
     */
    public function afficherDetails(): string
    {
        // Vérifie si un spectacle est en cours (débuté, mais pas terminé)
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

        if ($this->images) {
            $imagesHTML = "<div class='images-container'>" . implode('', $this->getImagesHTML()) . "</div>";
        } else {
            $imagesHTML = "";
        }

        return <<<HTML
                <div class="box">
                    <h3 class="title is-3">{$this->titre}</h3>
                    <p>$statut</p><br/>
                    <p><b>Artistes :</b> {$this->implodeArtistes()}</p>
                    <p><b>Style :</b> $this->style</p>
                    <p><b>Date :</b> {$this->getFormattedDate(false)}</p>
                    <p><b>Heure :</b> $this->horaire</p>
                    <p><b>Durée :</b> {$this->afficherDuree()}</p>
                    <p><b>Lieu :</b> {$this->lieu->getNom()} ({$this->lieu->getAdresse()})</p>
                    <p><b>Nombre de places :</b> {$this->lieu->getNbPlacesAssises()} assises, {$this->lieu->getNbPlacesDebout()} debout</p>
                    <p><b>Description :</b> $this->description</p>
                    $imagesHTML
                </div>
        HTML;
    }

}