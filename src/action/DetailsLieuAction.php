<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\exception\DateIncompatibleException;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action pour afficher les détails d'un lieu
 */
class DetailsLieuAction extends Action
{
    /**
     * Affiche les détails d'un lieu
     * @return string
     */
    public function execute(): string
    {
        $idLieu = $_GET['id'];
        $repository = NRVRepository::getInstance();
        $lieu = $repository->fetchLieu($idLieu);

        return $lieu->afficherDetails();
    }
}