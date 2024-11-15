<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use DateTime;
use Exception;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action d'ajout d'une soirée
 */
class AjouterSoireeAction extends Action
{

    /**
     * Retourne le formulaire d'ajout d'une soirée
     * @throws DateMalformedStringException Si la date n'est pas au bon format
     * @return string Formulaire d'ajout d'une soirée
     */
    private function getAddSoiree(): string
    {
        $repo = NRVRepository::getInstance();
        $lieux = $repo->getLieux();
        $images = $repo->getImages();

        $lieuOptions = "<option value='' selected disabled>Sélectionner un lieu</option>";
        foreach ($lieux as $lieu) {
            $lieuOptions .= "<option value='{$lieu->getId()}'>$lieu</option>";
        }
        $imageOptions = "";
        foreach ($images as $image) {
            $imageOptions .= "<option value='$image' data-image='resources/images/$image'>$image</option>";
        }

        return <<<HTML
    <section class="section">
        <h1 class="title">Ajouter une Soirée</h1>
        <form action="index.php?action=ajouter-soiree" method="post">
            <div class="field">
                <label class="label required" for="nom">Nom</label>
                <div class="control">
                    <input class="input" id="nom" type="text" name="nom" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="theme">Thème</label>
                <div class="control">
                    <input class="input" id="theme" type="text" name="theme">
                </div>
            </div>
            <div class="field">
                <label class="label required" for="date">Date et heure de début</label>
                <div class="control">
                    <input class="input" id="date" type="datetime-local" name="date" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="images">Images</label>
                <div class="image-field">
                    <div class="control select is-multiple">
                        <select class="input" id="images" name="images[]" multiple>
                            $imageOptions
                        </select>
                    </div>
                    <!-- Image de prévisualisation -->
                    <img id="imagePreview" class="preview-image" src="" alt="Prévisualisation de l'image">
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
                    <button class="button is-link">Ajouter</button>
                </div>
            </div>
        </form>
</section>
<script src="resources/js/hoverImage.js"></script>
HTML;
    }


    /**
     * Ajoute une soirée à la base de données
     * @throws Exception Si la soirée n'a pas pu être ajoutée
     * @return string Message de succès ou d'erreur
     */
    private function postAddSoiree(): string
    {
        if (!isset($_POST['nom'], $_POST['date'], $_POST['lieu'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }

        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $theme = filter_var($_POST['theme'], FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_NUMBER_INT);
        $images = $_POST['images'];
        try {
            // Crée un objet spectacle
            $soiree = new Soiree(null, $nom, $theme, new DateTime($date), new Lieu($lieu, '', '', 0, 0));
            $repo = NRVRepository::getInstance();
            // Ajoute le spectacle à la base de données et récupère son identifiant
            $soiree->setId($repo->addSoiree($soiree));
            //ajoute les images à la base de données
            $repo->addImagesToSoiree($soiree->getId(), $images);
            // Renvoie un message de succès
            return "<div class='notification is-success'>Soirée ajoutée avec succès</div>";
        } catch (Exception $e) {
            // Renvoie un message d'erreur
            return "<div class='notification is-danger'>Erreur lors de l'ajout de la soirée : {$e->getMessage()}</div>";
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
            return "Vous devez être connecté pour ajouter une soirée.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postAddSoiree();
        } else {
            return $this->getAddSoiree();
        }
    }

}