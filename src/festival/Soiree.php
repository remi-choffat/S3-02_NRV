<?php
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
     * constructeur de la classe
     * @param string $nom
     * @param string $theme
     * @param string $date
     * @param Lieu $lieu
     */
    public function __construct(string $nom, string $theme, string $date, Lieu $lieu){
        $this->nom = $nom;
        $this->theme = $theme;
        $this->date = $date;
        $this->lieu = $lieu;
        $this->spectacles = [];
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
     * ajouter un spectacle
     * verfiie si le spectacle n'est pas déjà dans la liste 
     * et si le lieu est le même que celui de la soirée 
     * @throws LieuIncompatibleException
     * @param Spectacle $spectacle
     */
    public  function ajouteSpectacle(Spectacle $spectacle){
        if(!in_array($spectacle, $this->spectacles)){
            if($spectacle->getLieu()->equals($this->lieu)){
                $this->spectacles[] = $spectacle;
            }else{
                throw new LieuIncompatibleException();
            }
        }
    }
    /**
     * supprimer un spectacle
     * @param Spectacle $spectacle
     */
    public function supprimeSpectacle(Spectacle $spectacle){
        $key = array_search($spectacle, $this->spectacles);
        if($key !== false){
            unset($this->spectacles[$key]);
        }
    }
}