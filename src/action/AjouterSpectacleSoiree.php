<?php
namespace iutnc\nrv\action;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\repository\NRVRepository;
/**
 * Classe pour ajouter un spectacle à une soirée
 */
class AjouterSpectacleSoiree extends Action
{

    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * getForm
     * Formulaire qui liste les spectacles de la soirée séléctionné
     */
    public function getForm(): string {
        $repo = NRVRepository::getInstance();
        $idSoiree = $_GET['id'];
        $listeSpectacleSoiree =  $repo->getSoiree( $idSoiree )->getSpectacles();
        $listeSpectacle = $repo->getSpectacles( ); 
        $listeSpectaclePouvantEtreAjoute = [];
        foreach($listeSpectacle as $spectacle){
            if(!in_array($spectacle, $listeSpectacleSoiree) && 
            $spectacle->getLieu()->equals($listeSpectacleSoiree[0]->getLieu()) &&
            $spectacle->getDate() == $listeSpectacleSoiree[0]->getDate()){
                $listeSpectaclePouvantEtreAjoute[] = $spectacle;
            }
        }
        return '';
    }

    public function execute(): string
    {
        // TODO: Implement execute() method.
        return '';
    }
}