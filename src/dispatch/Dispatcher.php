<?php

declare(strict_types=1);

namespace iutnc\nrv\dispatch;

use DateMalformedStringException;
use iutnc\nrv\action\AjouterSpectaclePrefAction;
use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\DetailsSoireeAction;
use iutnc\nrv\action\DetailsSpectacleAction;
use iutnc\nrv\action\ListeSoireesAction;
use iutnc\nrv\action\ListeSpectaclePrefAction;
use iutnc\nrv\action\ListeSpectaclesAction;
use iutnc\nrv\action\SupprimerSpectaclePrefAction;

class Dispatcher
{

    private ?string $action;

    function __construct(?string $action)
    {
        $this->action = $action;
    }


    /**
     * Exécute l'action demandée
     * @throws DateMalformedStringException
     */
    public function run(): void
    {
        $action = match ($this->action) {
            'liste-spectacles' => new ListeSpectaclesAction(),
            'details-spectacle' => new DetailsSpectacleAction(),
            'liste-soirees' => new ListeSoireesAction(),
            'details-soiree' => new DetailsSoireeAction(),
            'ajouter-pref' => new AjouterSpectaclePrefAction(),
            'supprimer-pref' => new SupprimerSpectaclePrefAction(),
            'liste-favoris' => new ListeSpectaclePrefAction(),
            default => new DefaultAction()
        };
        $html = $action->execute();
        $this->renderPage($html);
    }


    /**
     * Affiche la page HTML
     */
    private function renderPage($html): void
    {
        $page = <<<HTML
<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css'>
<!--    <link rel='icon' type='image/png' href='resources/logo.png'>-->
    <link rel='stylesheet' type='text/css' href='resources/style.css'>
    <link rel="icon" type="image/png" href="resources/logo.png">
    <title>Nancy Rock Vibration 🎶</title>
    </head>
    <body>
    <div class='header'>
       <h1 class="title">
            <a href='?action=default'><img src='resources/logo.png' style='height: 50px;' alt='NRV'/></a>
            Nancy Rock Vibration 🎶
       </h1>
       <nav>
            <ul>
                <li><a href='?action=liste-spectacles'>Liste des spectacles</a></li>
                <li><a href='?action=liste-soirees'>Liste des soirées</a></li>
            </ul>
       </nav>
       <br/>
    </div>
    $html
    </body>
    </html>
HTML;

        echo $page;
    }

}