<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

class DetailsSpectacleAction extends Action
{
    /**
     * Affiche les détails d'un spectacle
     * @return string
     * @throws DateMalformedStringException
     */
    public function execute(): string
    {
        $idSpectacle = $_GET['id'];
        $spectacle = NRVRepository::getInstance()->getSpectacle($idSpectacle);

        return $spectacle->afficherDetails();
    }
}