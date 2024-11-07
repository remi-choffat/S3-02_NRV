<?php

namespace iutnc\nrv\action;

class ListeSpectaclePrefAction extends Action
{

    public function execute(): string
    {
        $html = "<h2 class='subtitle'>Vos spectacles préférés</h2>";
        $spectacles = $_SESSION["favoris"];
        foreach ($spectacles as $spectacle) {
            $html .= $spectacle->afficherResume();
        }

        return $html;
    }
}