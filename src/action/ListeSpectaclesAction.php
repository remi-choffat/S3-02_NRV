<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class ListeSpectaclesAction extends Action
{
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