<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

class DetailsSoireeAction extends Action
{
    /**
     * Affiche les détails d'une soirée
     * @return string
     * @throws DateMalformedStringException
     */
    public function execute(): string
    {
        $idSoiree = $_GET['id'];
        $soiree = NRVRepository::getInstance()->getSoiree($idSoiree);

        return $soiree->afficherDetails();
    }
}