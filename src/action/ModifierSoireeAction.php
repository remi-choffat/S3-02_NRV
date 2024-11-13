<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use DateTime;
use Exception;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action de modification d'une soirée
 */
class ModifierSoireeAction extends Action
{
    /**
     * Retourne le formulaire de modification d'une soirée
     * @param int $id L'ID du spectacle à modifier
     * @throws DateMalformedStringException Si la date n'est pas au bon format
     * @return string Formulaire de modification d'une soirée
     */
    private function getUpdateSoiree(int $id): string
    {
        $repo = NRVRepository::getInstance();
        $soiree = $repo->getSoiree($id);
        $lieux = $repo->getLieux();

        $lieuOptions = "<option value='' selected disabled>Sélectionner un lieu</option>";
        foreach ($lieux as $lieu) {
            $selected = $lieu->getId() === $soiree->getLieu()->getId() ? 'selected' : '';
            $lieuOptions .= "<option value='{$lieu->getId()}' $selected>$lieu</option>";
        }

        return <<<HTML
<section class="section">
    <h1 class="title">Modifier une Soirée</h1>
    <form action="index.php?action=modifier-soiree&id={$id}" method="post">
        <div class="field">
            <label class="label required" for="nom">Nom</label>
            <div class="control">
                <input class="input" id="nom" type="text" name="nom" value="{$soiree->getNom()}" required>
            </div>
        </div>
        <div class="field">
            <label class="label required" for="theme">Thème</label>
            <div class="control">
                <input class="input" id="theme" type="text" name="theme" value="{$soiree->getTheme()}" required>
            </div>
        </div>
        <div class="field">
            <label class="label required" for="date">Date</label>
            <div class="control">
                <input class="input" id="date" type="datetime-local" name="date" value="{$soiree->getDate()->format('Y-m-d\TH:i')}" required>
            </div>
        </div>
        <div class="field">
            <label class="label required" for="lieu">Lieu</label>
            <div class="control">
                <select class="input" id="lieu" name="lieu" required>
                    $lieuOptions
                </select>
            </div>
        </div>
        <br/>
        <div class="field">
            <div class="control">
                <button class="button is-link">Modifier</button>
            </div>
        </div>
    </form>
</section>
HTML;
    }


    /**
     * Met à jour une soirée dans la base de données
     * @throws Exception Si la soirée n'a pas pu être mis à jour
     * @return string Message de succès ou d'erreur
     */
    private function postUpdateSoiree(int $id): string
    {
        if (!isset($_POST['nom'], $_POST['theme'], $_POST['date'], $_POST['lieu'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }

        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $theme = filter_var($_POST['theme'], FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_NUMBER_INT);

        try {
            // Crée un objet soiree
            $soiree = new Soiree($id, $nom, $theme, new DateTime($date), new Lieu($lieu, '', '', 0, 0));
            $repo = NRVRepository::getInstance();
            // Met à jour la soiree dans la base de données
            $repo->updateSoiree($soiree);
            // Renvoie un message de succès
            return "<div class='notification is-success'>Soirée mise à jour avec succès</div>";
        } catch (Exception $e) {
            // Renvoie un message d'erreur
            return "<div class='notification is-danger'>Erreur lors de la mise à jour de la soirée : {$e->getMessage()}</div>";
        }
    }


    /**
     * Exécute l'action en fonction de la méthode HTTP
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function execute(): string
    {
        if (!isset($_SESSION['utilisateur'])) {
            return "Vous devez être connecté pour modifier une soirée.";
        }

        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postUpdateSoiree($id);
        } else {
            return $this->getUpdateSoiree($id);
        }
    }
}