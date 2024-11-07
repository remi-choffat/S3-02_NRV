<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class ListeSoireesAction extends Action
{
    /**
     * fonction execute qui permet de lister les soirées de la soirée 
     * @return string
     */
    public function execute(): string
    {
        $html = "<h2 class='subtitle'>Liste des soirées du festival</h2>";
        $soirees = NRVRepository::getInstance()->getSoirees();
        foreach ($soirees as $soiree) {
            $html .= $soiree->afficherResume();
        }

        return $html;
    }
}