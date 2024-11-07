<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

class ListeSpectaclesAction extends Action
{
    /**
     * Fonction execute qui permet de lister les spectacles de la soirée
     * @return string affichage des spectacles favoris
     * @throws DateMalformedStringException
     */
    public function execute(): string
    {
        $html = "<h2 class='subtitle'>Liste des spectacles du festival</h2>";
        $spectacles = NRVRepository::getInstance()->getSpectacles();
        foreach ($spectacles as $spectacle) {
            $html .= $spectacle->afficherResume();
        }

        return $html;
    }
}