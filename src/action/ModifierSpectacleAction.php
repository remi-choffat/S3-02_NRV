<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use DateTime;
use Exception;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action de modification d'un spectacle
 */
class ModifierSpectacleAction extends Action
{
    /**
     * Retourne le formulaire de modification d'un spectacle
     * @param int $id L'ID du spectacle à modifier
     * @throws DateMalformedStringException Si la date n'est pas au bon format
     * @return string Formulaire de modification d'un spectacle
     */
    private function getUpdateSpectacle(int $id): string
    {
        $repo = NRVRepository::getInstance();
        $spectacle = $repo->getSpectacle($id);
        $soirees = $repo->getSoirees();
        $lieux = $repo->getLieux();
        $artistes = $repo->getArtistes();
        $images = $repo->getImages();

        $soireeOptions = "<option value=''>Aucune soirée</option>";
        foreach ($soirees as $soiree) {
            $selected = $soiree->getId() === $spectacle->getSoireeId() ? 'selected' : '';
            $soireeOptions .= "<option value='{$soiree->getId()}' $selected>{$soiree->getNom()}</option>";
        }

        $lieuOptions = "<option value='' selected disabled>Sélectionner un lieu</option>";
        foreach ($lieux as $lieu) {
            $selected = $lieu->getId() === $spectacle->getLieu()->getId() ? 'selected' : '';
            $lieuOptions .= "<option value='{$lieu->getId()}' $selected>$lieu</option>";
        }

        $artisteOptions = "";
        foreach ($artistes as $artiste) {
            $selected = in_array($artiste, $repo->fetchArtistes($id)) ? 'selected' : '';
            $artisteOptions .= "<option value='{$artiste->getId()}' $selected>{$artiste->getNomArtiste()}</option>";
        }
        $imageOptions = "";
        foreach ($images as $image) {
            $selected = in_array($image, $repo->getImagesSpectacle($id)) ? 'selected' : '';
            $imageOptions .= "<option value='{$image}' $selected data-image='images/$image'>{$image}</option>";
        }

        return <<<HTML
    <section class="section">
        <h1 class="title">Modifier un Spectacle</h1>
        <form action="index.php?action=modifier-spectacle&id={$id}" method="post">
            <div class="field">
                <label class="label required" for="nom">Nom</label>
                <div class="control">
                    <input class="input" id="nom" type="text" name="nom" value="{$spectacle->getTitre()}" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="style">Style</label>
                <div class="control">
                    <input class="input" id="style" type="text" name="style" value="{$spectacle->getStyle()}" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="date">Date et heure de début</label>
                <div class="control">
                    <input class="input" id="date" type="datetime-local" name="date" value="{$spectacle->getDate()->format('Y-m-d\TH:i')}" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="duree">Durée (minutes)</label>
                <div class="control">
                    <input class="input" id="duree" type="number" name="duree" value="{$spectacle->getDuree()}" required>
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
            <div class="field">
                <label class="label" for="artistes">Artistes</label>
                <div class="control select is-multiple">
                    <select class="input" id="artistes" name="artistes[]" multiple>
                        $artisteOptions
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label" for="images">Images</label>
                <div class="control select is-multiple">
                    <select class="input" id="images" name="images[]" multiple>
                        $imageOptions
                    </select>
                </div>
                <!-- Image de prévisualisation -->
                <img id="imagePreview" class="preview-image" src="" alt="Prévisualisation de l'image">
            </div>
            <div class="field">
                <label class="label" for="description">Description</label>
                <div class="control">
                    <textarea class="textarea" id="description" name="description">{$spectacle->getDescription()}</textarea>
                </div>
            </div>
            <div class="field">
                <label class="label" for="soiree">Soirée</label>
                <div class="control">
                    <select class="input" id="soiree" name="soiree">
                        $soireeOptions
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
<script src="src/js/hoverImage.js"></script>
HTML;
    }

    /**
     * Met à jour un spectacle dans la base de données
     * @throws Exception Si le spectacle n'a pas pu être mis à jour
     * @return string Message de succès ou d'erreur
     */
    private function postUpdateSpectacle(int $id): string
    {
        if (!isset($_POST['nom'], $_POST['style'], $_POST['date'], $_POST['duree'], $_POST['lieu'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }

        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $style = filter_var($_POST['style'], FILTER_SANITIZE_SPECIAL_CHARS);
        $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($_POST['duree'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $lieu = filter_var($_POST['lieu'], FILTER_SANITIZE_NUMBER_INT);
        if (!isset($_POST['soiree'])) {
            $soiree = null;
        } else {
            $soiree = filter_var($_POST['soiree'], FILTER_SANITIZE_NUMBER_INT);
            $soiree = $soiree === '' ? null : intval($soiree);
        }
        $artistes = array_map('intval', $_POST['artistes']);
        $images = $_POST['images'];

        try {
            // Crée un objet spectacle
            $spectacle = new Spectacle($id, $nom, new DateTime($date), $duree, $artistes, $style, new Lieu($lieu, '', '', 0, 0), $description, false, $soiree);
            $repo = NRVRepository::getInstance();
            // Met à jour le spectacle dans la base de données
            $repo->updateSpectacle($spectacle);
            // Supprime tous les artistes associés au spectacle et toutes les images
            $repo->removeArtistesFromSpectacle($spectacle->getId());
            $repo->removeImagesFromSpectacle($spectacle->getId());
            // Met à jour les artistes associés au spectacle et toutes les images
            $repo->addArtistesToSpectacle($spectacle->getId(), $artistes);
            $repo->addImagesToSpectacle($spectacle->getId(), $images);
            // Renvoie un message de succès
            return "<div class='notification is-success'>Spectacle mis à jour avec succès</div>";
        } catch (Exception $e) {
            // Renvoie un message d'erreur
            return "<div class='notification is-danger'>Erreur lors de la mise à jour du spectacle : {$e->getMessage()}</div>";
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
            return "Vous devez être connecté pour modifier un spectacle.";
        }

        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postUpdateSpectacle($id);
        } else {
            return $this->getUpdateSpectacle($id);
        }
    }
}