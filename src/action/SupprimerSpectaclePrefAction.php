<?php

namespace iutnc\nrv\action;

/**
 * Action de suppression d'un spectacle des favoris
 */
class SupprimerSpectaclePrefAction extends Action
{
    public function execute(): string
    {
        if (isset($_POST['spectacle']) && $this->http_method === "POST") {

            // Si le tableau $_SESSION["favoris"] existe et que l'ID de spectacle est dans le tableau
            if (isset($_SESSION["favoris"]) && in_array($_POST['spectacle'], $_SESSION["favoris"])) {
                // On récupère l'index du spectacle dans le tableau
                $index = array_search($_POST['spectacle'], $_SESSION["favoris"]);
                // On supprime le spectacle du tableau
                unset($_SESSION["favoris"][$index]);
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


