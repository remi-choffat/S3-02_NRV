<?php
declare(strict_types= 1);
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
    private string $nom;
    private string $theme;
    private string $date;
    private string $heureDebut;
    private Lieu $lieu;
    private array $spectacles;

    /**
     * Constructeur de la classe
     * @param string $nom
     * @param string $theme
     * @param string $date
     * @param Lieu $lieu
     */
    public function __construct(string $nom, string $theme, string $date, Lieu $lieu, string $heureDebut)
    {
        $this->nom = $nom;
        $this->theme = $theme;
        $this->date = $date;
        $this->lieu = $lieu;
        $this->spectacles = [];
        $this->heureDebut = $heureDebut;
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
     * et si le lieu est le même que celui de la soirée
     * @param Spectacle $spectacle
     * @throws LieuIncompatibleException
     */
    public function ajouteSpectacle(Spectacle $spectacle): void
    {
        if (!in_array($spectacle, $this->spectacles)) {
            if ($spectacle->getLieu()->equals($this->lieu)) {
                $this->spectacles[] = $spectacle;
            } else {
                throw new LieuIncompatibleException();
            }
        }
    }

    public function renduHtmlSimple():string
    {
        $affichage = "<h3>" . $this->nom . "</h3>" . "<br/>" . "<p>" . $this->theme . "</p>" . " - " . "<p>" . $this->date . "</p>" . " - " . "<p>" . $this->lieu . "</p>";
        return $affichage;
    }

    public function renduHtmlDetaille():string
    {
        $sortie = "<div class='list-spectacle'>";
        foreach ($this->spectacles as $spectacle){
            $sortie .= "<p>" . $spectacle . "</p>";
        }
        $sortie .= "</div>";
        $affichage = "<h3>" . $this->nom . "</h3>" . "<br/>" . "<p><b>" . "Theme : " . "</b>" . $this->theme . "</p>" . "<br/>" . "<p><b>" . "Date : " . "</b>" . $this->date . "</p>" . "<br/>" . "<p><b>" . "Débute à : " . "</b>". $this->heureDebut . "</p>" . "<br/>" . "<p><b>" . "Lieu : " . "</b>". $this->lieu->getNom() . "</p>" . "<br/>" . "<p>" . "Liste des spectacles :" . "<br/>" . $sortie . "</p>";
        return $affichage;
    }
}