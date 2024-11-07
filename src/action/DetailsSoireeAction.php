<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DetailsSoireeAction extends Action
{
    public function execute(): string
    {
        $idSoiree = $_GET['id'];
        $soiree = NRVRepository::getInstance()->getSoiree($idSoiree);

        return $soiree->afficherDetails();
    }
}