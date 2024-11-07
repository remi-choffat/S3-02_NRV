<?php
declare(strict_types=1);

namespace iutnc\nrv\festival;

use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\exception\LieuIncompatibleException;

/**
 * classe Soiree
 */
class Soiree
{
    /**
     * attribut de la classe
     */
    private int $id;
    private string $nom;
    private string $theme;
    private string $date;
    private string $heureDebut;
    private Lieu $lieu;
    private array $spectacles;

    /**
     * Constructeur de la classe
     * @param int $id
     * @param string $nom
     * @param string $theme
     * @param string $date
     * @param Lieu $lieu
     * @param string $heureDebut
     */
    public function __construct(int $id, string $nom, string $theme, string $date, Lieu $lieu, string $heureDebut)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->theme = $theme;
        $this->date = $date;
        $this->lieu = $lieu;
        $this->spectacles = [];
        $this->heureDebut = $heureDebut;
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
     * @return string
     */
    public function getDate(): string
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
     * getter de l'attribut lieu
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
     */
    public function ajouterSpectacle(Spectacle $spectacle): void
    {
        if (!in_array($spectacle, $this->spectacles)) {
//            if ($spectacle->getLieu()->equals($this->lieu)) {
            $this->spectacles[] = $spectacle;
//            } else {
//                throw new LieuIncompatibleException();
//            }
        }
    }

    public function afficherResume(): string
    {
        return <<<HTML
        <div class="box soiree">
            <h3 class="title is-4"><a href="?action=details-soiree&id={$this->getId()}">{$this->nom}</a></h3>
            <p><b>Thème : </b>{$this->theme}</p>
            <p><b>Date : </b>{$this->date}</p>
            <p><b>Débute à : </b>{$this->heureDebut}</p>
            <p><b>Lieu : </b>{$this->lieu->getNom()}</p>
        </div>
HTML;

    }

    public function afficherDetails(): string
    {
        $sortie = "<div class='box list-spectacle'>
        <h3 class='title is-3'>{$this->nom}</h3>
        <p><b>Thème : </b>{$this->theme}</p>
        <p><b>Date : </b>{$this->date}</p>
        <p><b>Débute à : </b>{$this->heureDebut}</p>
        <p><b>Lieu : </b>{$this->lieu->getNom()}</p>
        <br/>
        <h4 class='title is-4'>Spectacles :</h4>";
        foreach ($this->spectacles as $spectacle) {
            $sortie .= $spectacle->afficherResume();
        }
        $sortie .= "</div>";
        return $sortie;
    }
}