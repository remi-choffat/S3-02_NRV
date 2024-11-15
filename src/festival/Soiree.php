<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use DateTime;
use iutnc\nrv\exception\DateIncompatibleException;
use iutnc\nrv\exception\LieuIncompatibleException;
use iutnc\nrv\exception\SpectacleAssignationException;
use iutnc\nrv\exception\ThemeIncompatibleException;
use iutnc\nrv\repository\NRVRepository;

/**
 * Représente une soirée
 */
class Soiree
{

    /**
     * Attributs de la classe
     */
    private ?int $id;
    private string $nom;
    private ?string $theme;
    private DateTime $date;
    private string $heureDebut;
    private Lieu $lieu;
    private array $spectacles;
    private array $images;


    /**
     * Constructeur de la classe
     * @param int|null $id
     * @param string $nom
     * @param string|null $theme
     * @param DateTime $date
     * @param Lieu $lieu
     * @param array $spectacles
     */
    public function __construct(?int $id, string $nom, ?string $theme, DateTime $date, Lieu $lieu, array $spectacles = [])
    {
        $this->date = $date;
        $this->id = $id ?? -1;
        $this->nom = $nom;
        $this->theme = $theme;
        $this->spectacles = $spectacles;
        $this->verifierDate();
        $this->lieu = $lieu;
        $this->heureDebut = $date->format('H:i');
        if ($id !== null) {
            $this->images = NRVRepository::getInstance()->getImagesSoiree($this->id);
        } else {
            $this->images = [];
        }
    }


    /**
     * Renvoie, sous forme de tableau, le code HTML des images de la soirée
     * @return array
     */
    public function getImagesHTML(): array
    {
        $imagesHTML = [];
        foreach ($this->images as $image) {
            $imagesHTML[] = "<img src='resources/images/{$image}' alt='Image de la soirée {$this->nom}' class='soiree-image'>";
        }
        return $imagesHTML;
    }


    /**
     * Renvoie le code HTML de la première image de la soirée
     * @return string
     */
    public function getFirstImageHTML(): string
    {
        if (empty($this->images)) {
            return "";
        } else {
            return "<img src='resources/images/{$this->images[0]}' alt='Image de la soirée {$this->nom}' class='soiree-image-resume'>";
        }
    }


    /**
     * getter de l'attribut id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return void
     * @throws DateIncompatibleException envoie une erreur si la soirée commence à une heure valide
     */
    private function verifierDate(): void
    {
        if (((int)$this->date->format("H") > 5 && (int)$this->date->format("H") < 17)
            || ((int)$this->date->format("H") < 5 && $this->date->format("H:i") != "00:00")) {
            throw new DateIncompatibleException("L'horaire de début choisit pour la soirée doit être entre 17h et 5h");
        }
    }


    /**
     * getter de l'attribut nom
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }


    /**
     * getter de l'attribut theme
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }


    /**
     * getter de l'attribut date
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }


    /**
     * getter de l'attribut heureDebut
     * @return string
     */
    public function getHeureDebut(): string
    {
        return $this->heureDebut;
    }


    /**
     * Getter de l'attribut lieu
     * @return Lieu
     */
    public function getLieu(): Lieu
    {
        return $this->lieu;
    }


    /**
     * getter de l'attribut spectacles
     * @return array
     */
    public function getSpectacles(): array
    {
        return $this->spectacles;
    }


    /**
     * Ajoute un spectacle
     * Vérifie si le spectacle n'est pas déjà dans la liste
     * @param Spectacle $spectacle
     * @throws LieuIncompatibleException le spectacle ne peut pas être ajouter à la soirée
     * @throws ThemeIncompatibleException le spectacle n'est pas du même thème que la soirée donc il n'est pas ajoutée
     * @throws DateIncompatibleException le spectacle n'as pas lieu pendant le soir ou débute avant le début de la soiré
     * @throws SpectacleAssignationException le spectacle a lieu en même temps qu'un autre
     */
    public function ajouterSpectacle(Spectacle $spectacle): void
    {
        $spectacle->verifierDatePourSoiree();
        if ($spectacle->getDate()->getTimestamp() - $this->date->getTimestamp() > 0) {
            throw new DateIncompatibleException("Le Spectacle débute avant la soirée");
        }
        $this->spectacleSansChevauchement($spectacle);
        if (!in_array($spectacle, $this->spectacles)) {
            if ($this->theme == $spectacle->getStyle()) {
                if ($spectacle->getLieu()->equals($this->lieu)) {
                    $this->spectacles[] = $spectacle;
                } else {
                    throw new LieuIncompatibleException();
                }
            } else throw new ThemeIncompatibleException("Les thèmes ne sont pas identiques");
        }
    }

    /**
     * @param Spectacle $spectacle
     * @return void vérifie que les horaires des spectacles d'une soirée ne ce chevauche pas
     */

    private function spectacleSansChevauchement(Spectacle $spectacle)
    {
        $datesmin = [];
        $datesmax = [];
        foreach ($this->spectacles as $sp) {
            $datesmin[] = $sp->getDate();
        }
        foreach ($this->spectacles as $sp) {
            $datesmax[] = $sp->getFin();
        }
        $datemin = null;
        $datemax = null;
        foreach ($datesmin as $date) {
            $time = $date->diff($this->date);
            if (is_null($datemin)) {
                if ($time->h <= 12) {
                    $datemin = $date;
                }
            } else {
                $datemax = $date;
                break;
            }
        }
        if ($datemin->getTimestamp() - $spectacle->getDate()->getTimestamp() > 0 ||
            $spectacle->getDate()->getTimestamp() - $datemax->getTimestamp() > 0) {
            throw new SpectacleAssignationException();
        }
    }


    /**
     * Affiche le résumé de la soirée en html
     * @return string
     */
    public function afficherResume(): string
    {
        // Affiche le menu de modification et d'annulation si l'utilisateur est connecté
        if (isset($_SESSION['utilisateur'])) {
            $menu = <<<HTML
            <div class="menu">
                <button class="menu-btn">⋮</button>
                <div class="menu-content">
                    <a href="index.php?action=modifier-soiree&id={$this->id}">Modifier</a>
                </div>
            </div>
        HTML;
        } else {
            $menu = "";
        }

        $firstImageHTML = $this->getFirstImageHTML();

        $theme = $this->theme ? "<p><b>Thème : </b>$this->theme</p>" : "";
        return <<<HTML
        <div class="box soiree">
            <div class="soiree-header">
                <h3 class="title is-4"><a href="?action=details-soiree&id={$this->getId()}">{$this->nom}</a></h3>
                $menu        
            </div>    
            <div class='soiree-content'>
                <div class="soiree-text">
                    $theme
                    <p><b>Date : </b>{$this->date->format('d/m/Y')}</p>
                    <p><b>Débute à : </b>$this->heureDebut</p>
                    <p><b>Lieu : </b>{$this->lieu->getNom()}</p>
                </div>
                $firstImageHTML
            </div>
        </div>
HTML;

    }


    /**
     * Affiche les détails de la soirée en HTML
     * @return string
     */
    public function afficherDetails(): string
    {

        if ($this->images) {
            $imagesHTML = "<div class='images-container'>" . implode('', $this->getImagesHTML()) . "</div>";
        } else {
            $imagesHTML = "";
        }

        $lienLieu = "<a href='?action=details-lieu&id={$this->lieu->getId()}' title='Voir les détails du lieu - {$this->lieu->getNom()}'>{$this->lieu->getNom()}</a>";

        $theme = $this->theme ? "<p><b>Thème : </b>$this->theme</p>" : "";
        $sortie = "<div class='box list-spectacle'>
        <h3 class='title is-3'>{$this->nom}</h3>
        $theme
        <p><b>Date :</b> {$this->date->format('d/m/Y')}</p>
        <p><b>Débute à :</b> {$this->heureDebut}</p>
        <p><b>Finit à :</b> {$this->getFin()->format("H:i")}</p>
        <p><b>Lieu :</b> $lienLieu ({$this->lieu->getAdresse()})</p>
        <br/>
        $imagesHTML
        <br/><br/>
        <h4 class='title is-4'>Spectacles :</h4>";

        if (sizeof($this->spectacles) == 0) {
            $sortie .= "<p><strong>Aucun spectacle</strong></p>";
        }
        foreach ($this->spectacles as $spectacle) {
            $sortie .= $spectacle->afficherResume();
        }
        $sortie .= "</div>";
        return $sortie;
    }


    /**
     * Renvoie la date (et l'heure) de fin de la soirée (fin du dernier spectacle)
     * @return DateTime retourne la date avec l'heure de fin
     */
    public function getFin(): DateTime
    {
        // Si la liste des spectacles est vide, on renvoie la date de début de la soirée
        if (sizeof($this->getSpectacles()) == 0) {
            return $this->date;
        }

        // Sinon, on renvoie la date de fin du dernier spectacle
        return $this->getSpectacles()[sizeof($this->getSpectacles()) - 1]->getFin();
    }


    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}