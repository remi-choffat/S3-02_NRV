<?php

namespace iutnc\nrv\action;

class AjouterSpectaclePrefAction extends Action
{
    /**
     * @return string retourne une chaine pour informer si l'action est réussi
     */
    public function execute(): string
    {
        $html = "";
        if (isset($_POST['spectacle']) && $this->http_method === "POST") {
            if(!isset($_SESSION["favoris"])){
                $_SESSION["favoris"]=[];
            }
            $_SESSION["favoris"][] = $_POST['spectacle'];
            $html = "Ajout du spectacle aux favoris";
        } else {
            $html = "🐖 à l'abattoire";
        }
        return $html;
    }
}