<?php

declare(strict_types=1);

namespace iutnc\nrv\dispatch;

use DateMalformedStringException;
use iutnc\nrv\action\AjouterArtisteAction;
use iutnc\nrv\action\AjouterLieuAction;
use iutnc\nrv\action\AjouterSoireeAction;
use iutnc\nrv\action\AjouterSpectacleAction;
use iutnc\nrv\action\AjouterSpectaclePrefAction;
use iutnc\nrv\action\AnnulerSpectacleAction;
use iutnc\nrv\action\Deconnexion;
use iutnc\nrv\action\DefaultAction;
use iutnc\nrv\action\DetailsSoireeAction;
use iutnc\nrv\action\DetailsSpectacleAction;
use iutnc\nrv\action\ErrorAction;
use iutnc\nrv\action\ListeSoireesAction;
use iutnc\nrv\action\ListeSpectaclePrefAction;
use iutnc\nrv\action\ListeSpectaclesAction;
use iutnc\nrv\action\ModifierSoireeAction;
use iutnc\nrv\action\ModifierSpectacleAction;
use iutnc\nrv\action\RestaurerSpectacleAction;
use iutnc\nrv\action\SupprimerSpectaclePrefAction;
use iutnc\nrv\action\Inscription;
use iutnc\nrv\action\Connexion;
use iutnc\nrv\action\AjouterImageAction;
use iutnc\nrv\action\UnknownAction;
use iutnc\nrv\auth\AuthProvider;
use iutnc\nrv\auth\Authz;
use Exception;
use iutnc\nrv\exception\UnauthorizedActionException;

/**
 * Dispatche les actions
 */
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
        try {
            $action = match ($this->action) {
                "null", 'default' => new DefaultAction(),
                'liste-spectacles' => new ListeSpectaclesAction(),
                'details-spectacle' => new DetailsSpectacleAction(),
                'liste-soirees' => new ListeSoireesAction(),
                'details-soiree' => new DetailsSoireeAction(),
                'ajouter-pref' => new AjouterSpectaclePrefAction(),
                'supprimer-pref' => new SupprimerSpectaclePrefAction(),
                'liste-favoris' => new ListeSpectaclePrefAction(),
                'inscription' => new Inscription(0),
                'connexion' => new Connexion(),
                'deconnexion' => new Deconnexion(1),
                'ajouter-spectacle' => new AjouterSpectacleAction(1),
                'ajouter-soiree' => new AjouterSoireeAction(1),
                'ajouter-lieu' => new AjouterLieuAction(1),
                'ajouter-artiste' => new AjouterArtisteAction(1),
                'modifier-spectacle' => new ModifierSpectacleAction(1),
                'modifier-soiree' => new ModifierSoireeAction(1),
                'ajouter-image' => new AjouterImageAction(1),
                'annuler-spectacle' => new AnnulerSpectacleAction(1),
                'restaurer-spectacle' => new RestaurerSpectacleAction(1),
                'error' => new ErrorAction(),
                default => new UnknownAction(),
            };
            $html = $action->execute();
        }
        catch (UnauthorizedActionException $e){
            $html=$e->getMessage();
        }
        $this->renderPage($html);
    }


    /**
     * Affiche la page HTML
     */
    private function renderPage($html): void
    {
        // Affiche le lien d'ajout d'un utilisateur aux ADMIN
        try {
            Authz::checkRole(0);
            $boutonsAdmin = "<li><a href='?action=inscription'>Inscrire un utilisateur</a></li>";
        } catch (Exception $e) {
            $boutonsAdmin = "";
        }

        // Vérifie si un utilisateur est connecté
        try {
            $user = AuthProvider::getSignedInUser();
            $name = "<span id='username' title='{$user->getEmail()}'>{$user->getNom()}</span>";
            $deconnexion = "(<a href='?action=deconnexion'>Déconnexion</a>)";
            $boutonsStaffAdmin = <<<HTML
<li>
<div class="dropdown">
    <button class="dropbtn">Ajouter &#9662;</button>
    <div class="dropdown-content">
        <a href="?action=ajouter-spectacle">Ajouter un spectacle</a>
        <a href="?action=ajouter-soiree">Ajouter une soirée</a>
        <a href="?action=ajouter-lieu">Ajouter un lieu</a>
        <a href="?action=ajouter-artiste">Ajouter un artiste</a>
        <a href="?action=ajouter-image">Ajouter une image</a>
    </div>
</div>
</li>
HTML;
        } catch (Exception $e) {
            $name = "";
            $deconnexion = "<a href='?action=connexion'>Connexion</a>";
            $boutonsStaffAdmin = "";
        }

        $page = <<<HTML
<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel='stylesheet' type='text/css' href='resources/style.css'>
    <link rel="icon" type="image/png" href="resources/logo.png">
    <title>Nancy Rock Vibration</title>
    </head>
    <body>
    <div class='container'>
        <div class='header'>
            <h1 class="title">
                <a href='?action=default'><img src='resources/logo.png' style='height: 50px;' alt='NRV'/></a>
                Nancy Rock Vibration 🎶
            </h1>
            <nav>
                <ul>
                    <li><a href='?action=default'>Accueil</a></li>
                    <li><a href='?action=liste-spectacles'>Liste des spectacles</a></li>
                    <li><a href='?action=liste-soirees'>Liste des soirées</a></li>
                    <li><a href='?action=liste-favoris'>Spectacles favoris</a></li>
                    $boutonsStaffAdmin
                    $boutonsAdmin
                </ul>
            </nav>
            <br/>
        </div>
        <div id="contenuPage" class='content'>
            $html
        </div>
        <footer class='footer'>
            <div class='content has-text-centered'>
                <p>
                    <strong>Nancy Rock Vibration</strong> by Les Détraqués
                </p>
                <p>$name $deconnexion</p>
            </div>
        </footer>
    </div>
    </body>
    </html>
HTML;

        echo $page;
    }

}