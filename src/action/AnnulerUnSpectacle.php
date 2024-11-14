<?php

namespace iutnc\nrv\action;

use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\repository\NRVRepository;

class AnnulerUnSpectacle extends Action{

    public function execute(): string
    {
        $idSpectacle = $_GET['id'];
        $repository = NRVRepository::getInstance();
        $spectacle = $repository->getSpectacle($idSpectacle);

        $spectacle->SetAnnule();

        $repository->updateSpectacle($newSpect);

        return <<<HTML
<p>Balls</p>
HTML;
    }
}