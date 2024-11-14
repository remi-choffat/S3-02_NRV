<?php

namespace iutnc\nrv\action;

/**
 * Action pour ajouter un spectacle aux favoris
 */
class AjouterSpectaclePrefAction extends Action
{
    /**
     * Méthode pour ajouter un spectacle aux favoris
     * @return string retourne une chaine pour informer si l'action est réussi
     */
    public function execute(): string
    {
        var_dump($_POST);
        if (isset($_POST['spectacle']) && $this->http_method === "POST") {
            if (!isset($_SESSION["favoris"])) {
                $_SESSION["favoris"] = [];
            }
            $_SESSION["favoris"][] = $_POST['spectacle'];
            $html = "Ajout du spectacle aux favoris ⭐";
        } else {
            $html = "Aucun spectacle n'est à ajouter aux favoris";
        }
        return $html;
    }
}