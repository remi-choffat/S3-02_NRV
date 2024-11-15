<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use DateTime;
use iutnc\nrv\exception\DateIncompatibleException;
use iutnc\nrv\exception\LieuIncompatibleException;
use iutnc\nrv\exception\SpectacleAssignationException;

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
     * @throws DateIncompatibleException le spectacle n'as pas lieu pendant le soir ou débute avant le début de la soiré
     * @throws SpectacleAssignationException le spectacle a lieu en même temps qu'un autre
     */
    public function ajouterSpectaclePossible(Spectacle $spectacle): void
    {
        $time = $spectacle->getDate()->format("U") - ($this->getDate()->format("U"));
        $time2 = $spectacle->getDate()->format("U") - ($this->getFinMax()->format("U"));
        if ($time < 0) {
            throw new DateIncompatibleException("Le Spectacle débute avant la soirée");
        } elseif ($time2 > 0) {
            throw new DateIncompatibleException("Le Spectacle débute après la soirée");
        }
        $spectacle->verifierDatePourSoiree();
        $this->spectacleSansChevauchement($spectacle);
        if (!$spectacle->getLieu()->equals($this->lieu)) {
                    throw new LieuIncompatibleException();
        }

    }

    public function getFinMax(): DateTime
    {
        if ($this->date->format("H") >= 17) {
            return $this->date->setTime(5, 0)->modify("+1 day");
        } else {
            return $this->date->setTime(5, 0);
        }

    }

    /**
     * @param Spectacle $spectacle
     * @return void vérifie que les horaires des spectacles d'une soirée ne ce chevauche pas
     */

    private function spectacleSansChevauchement(Spectacle $spectacle)
    {
        foreach ($this->spectacles as $sp) {
            // quand $sp->getFin()->format("U") - $spectacle->getDate()->format("U") >0 le spectacle comence après
            // quand $sp->getFin()->format("U") - $spectacle->getDate()->format("U") <0 le spectacle comence avant
                if ($sp->getFin()->format("U") - $spectacle->getDate()->format("U") < 0) {
                    // donc faut vérif si le spectacle finit avant le commencement de celui déjà existant
                    // quand $spectacle->getFin()->format("U") - $sp->getDate()->format("U")<0 le spectacle finit avant qu'un autre commence
                    //quand $spectacle->getFin()->format("U") - $sp->getDate()->format("U")>0 le spectacle finit pendant qu'un autre est en cours
                    if($spectacle->getFin()->format("U") - $sp->getDate()->format("U")>0){
                        //donc erreur
                        throw new SpectacleAssignationException("Le Spectacle à lieu pendant un autre spectacle");
                    }
                }
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

        $theme = $this->theme ? "<p><b>Thème : </b>$this->theme</p>" : "";
        return <<<HTML
        <div class="box soiree">
        <div class="spectacle-header">
            <h3 class="title is-4"><a href="?action=details-soiree&id={$this->getId()}">{$this->nom}</a></h3>
            $menu            
        </div>
            $theme
            <p><b>Date : </b>{$this->date->format('d/m/Y')}</p>
            <p><b>Débute à : </b>$this->heureDebut</p>
            <p><b>Lieu : </b>{$this->lieu->getNom()}</p>
        </div>
HTML;

    }


    /**
     * Affiche les détails de la soirée en HTML
     * @return string
     */
    public function afficherDetails(): string
    {
        $theme = $this->theme ? "<p><b>Thème : </b>$this->theme</p>" : "";
        $sortie = "<div class='box list-spectacle'>
        <h3 class='title is-3'>{$this->nom}</h3>
        $theme
        <p><b>Date : </b>{$this->date->format('d/m/Y')}</p>
        <p><b>Débute à : </b>{$this->heureDebut}</p>
        <p><b>Finit à : </b>{$this->getFin()->format("H:i")}</p>
        <p><b>Lieu : </b>{$this->lieu->getNom()}</p>
        <br/>
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