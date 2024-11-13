<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use DateTime;
use Exception;
use iutnc\nrv\festival\Artiste;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action d'ajout d'un artiste
 */
class AjouterArtisteAction extends Action
{

    /**
     * Retourne le formulaire d'ajout d'un artiste
     * @return string Formulaire d'ajout d'un artiste
     */
    private function getAddArtiste(): string
    {
        return <<<HTML
    <section class="section">
        <h1 class="title">Ajouter un artiste</h1>
        <form action="index.php?action=ajouter-artiste" method="POST">
            <div class="field">
                <label class="label required" for="nom">Nom de l'artiste</label>
                <div class="control">
                    <input class="input" id="nom" type="text" name="nom" required>
                </div>
            </div>
            <br/>
            <div class="field">
                <div class="control">
                    <button class="button is-link">Ajouter</button>
                </div>
            </div>
        </form>
</section>
HTML;
    }


    /**
     * Ajoute un artiste à la base de données
     * @throws Exception Si l'artiste n'a pas pu être ajouté
     * @return string Message de succès ou d'erreur
     */
    private function postAddArtiste(): string
    {
        if (!isset($_POST['nom'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }

        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);

        try {
            // Crée un objet artiste
            $artiste = new Artiste(null, $nom);
            $repo = NRVRepository::getInstance();
            // Ajoute l'artiste à la base de données et récupère son identifiant
            $artiste->setId($repo->addArtiste($artiste));
            // Renvoie un message de succès
            return "<div class='notification is-success'>Artiste ajouté avec succès</div>";
        } catch (Exception $e) {
            // Renvoie un message d'erreur
            return "<div class='notification is-danger'>Erreur lors de l'ajout de l'artiste : {$e->getMessage()}</div>";
        }
    }


    /**
     * Exécute l'action en fonction de la méthode HTTP
     * @throws Exception
     */
    public function execute(): string
    {
        if (!isset($_SESSION['utilisateur'])) {
            return "Vous devez être connecté pour ajouter un artiste.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postAddArtiste();
        } else {
            return $this->getAddArtiste();
        }
    }

}