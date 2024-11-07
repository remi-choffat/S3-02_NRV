<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class DetailsSpectacleAction extends Action
{
    public function execute(): string
    {
        $idSpectacle = $_GET['id'];
        $spectacle = NRVRepository::getInstance()->getSpectacle($idSpectacle);

        return "<div class='box'>
            <h3 class='title is-3'>{$spectacle['titre']}</h3>
            <p><b>Date :</b> {$spectacle['date']}</p>
            <p><b>Heure :</b> {$spectacle['horaire']}</p>
            <p><b>Lieu :</b> {$spectacle['lieu']}</p>
            <p><b>Artiste :</b> {$spectacle['artiste']}</p>
            <p><b>Nombre de places :</b> {$spectacle['nb_places']}</p>
            <p><b>Description :</b> {$spectacle['description']}</p>
            </div>";

        // TODO - Pour le moment, l'affichage est fait en récupérant les informations de la liste de spectacles fictifs (Repository).
        //  Créer une méthode string afficher() dans Spectacle et dans Soiree.
    }
}