<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DetailsSpectacleAction extends Action
{
    /**
     * fonction execute qui permet d'afficher les détails d'un spectacle
     * @return string
     */
    public function execute(): string
    {
        $idSpectacle = $_GET['id'];
        $spectacle = NRVRepository::getInstance()->getSpectacle($idSpectacle);

        return $spectacle->afficherDetails();
    }
}