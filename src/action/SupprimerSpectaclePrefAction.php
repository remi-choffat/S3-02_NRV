<?php

namespace iutnc\nrv\action;

class SupprimerSpectaclePrefAction extends Action{
    public function execute(): string
    {
        $html = "";
        if(isset($_POST['spectacle'])&& isset($_POST['idfavoris']) && $this->http_method === "POST"){
            $s = $_POST['spectacle'];
            if(isset($_SESSION["favoris"][$_POST['idfavoris']])){
                unset($_SESSION["favoris"][$_POST['idfavoris']]);
                $html = "Le spectacle à bien était retiré des favoris";
            }else{
                $html = "Le spectacle n'est pas dans les favoris";
            }
        }
        else{
            $html = "erreur dans la requête post";
        }
        return $html;
    }
}


