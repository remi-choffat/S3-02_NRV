<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

class ListeSoireesAction extends Action
{
    /**
     * Liste les soirées du festival
     * @return string
     * @throws DateMalformedStringException
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