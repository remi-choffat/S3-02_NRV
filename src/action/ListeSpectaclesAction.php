<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class ListeSpectaclesAction extends Action
{
    /**
     * fonction execute qui permet de lister les spectacles de la soirée
     * @return string
     */
    public function execute(): string
    {
        $html = "<h2 class='subtitle'>Liste des spectacles du festival</h2>";
        $spectacles = $_SESSION["favoris"];
        foreach ($spectacles as $spectacle) {
            $html .= $spectacle->afficherResume();
        }

        return $html;
    }
}