<?php

namespace iutnc\nrv\action;

class SupprimerSpectaclePrefAction extends Action
{
    public function execute(): string
    {
        if (isset($_POST['spectacle']) && isset($_POST['idfavoris']) && $this->http_method === "POST") {
            if (isset($_SESSION["favoris"][$_POST['idfavoris']])) {
                unset($_SESSION["favoris"][$_POST['idfavoris']]);
                $html = "Le spectacle à bien été retiré des favoris";
            } else {
                $html = "Le spectacle n'est pas dans les favoris";
            }
        } else {
            $html = "Erreur dans la requête POST";
        }
        return $html;
    }
}


