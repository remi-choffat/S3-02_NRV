<?php

declare(strict_types=1);

namespace iutnc\nrv\dispatch;

use iutnc\nrv\action\DefaultAction;

class Dispatcher
{

    private ?string $action;

    function __construct(?string $action)
    {
        $this->action = $action;
    }


    /**
     * Exécute l'action demandée
     */
    public function run(): void
    {
        $action = match ($this->action) {
            default => new DefaultAction(),
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
    <title>NRV</title>
    </head>
    <body>
    <div class='header'>
       <nav>
            <ul>
                <li><a href='?action=default'>Accueil</a></li>
                <li><a href='?action=#'>Action 1</a></li>
                <li><a href='?action=#'>Action 2</a></li>
            </ul>
        </nav>
    </div>
    <hr/>
    <br/>
    $html
    </body>
    </html>
HTML;

        echo $page;
    }

}