<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use DateTime;
use Exception;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Spectacle;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action d'ajout d'un spectacle
 */
class AjouterSpectacleAction extends Action
{

    /**
     * Retourne le formulaire d'ajout d'un spectacle
     * @throws DateMalformedStringException Si la date n'est pas au bon format
     * @return string Formulaire d'ajout d'un spectacle
     */
    private function getAddSpectacle(): string
    {
        $repo = NRVRepository::getInstance();
        $soirees = $repo->getSoirees();
        $lieux = $repo->getLieux();
        $artistes = $repo->getArtistes();
        $images = $repo->getImages();

        $soireeOptions = "<option value='' selected disabled>Sélectionner une soirée</option>";
        foreach ($soirees as $soiree) {
            $soireeOptions .= "<option value='{$soiree->getId()}'>{$soiree->getNom()}</option>";
        }

        $lieuOptions = "<option value='' selected disabled>Sélectionner un lieu</option>";
        foreach ($lieux as $lieu) {
            $lieuOptions .= "<option value='{$lieu->getId()}'>$lieu</option>";
        }

        $artisteOptions = "";
        foreach ($artistes as $artiste) {
            $artisteOptions .= "<option value='{$artiste->getId()}'>{$artiste->getNomArtiste()}</option>";
        }

        $imageOptions = "";
        foreach ($images as $image) {
            $imageOptions .= "<option value='$image' data-image='resources/images/$image'>$image</option>";
        }


        return <<<HTML
    <section class="section">
        <h1 class="title">Ajouter un Spectacle</h1>
        <form action="index.php?action=ajouter-spectacle" method="post">
            <div class="field">
                <label class="label required" for="nom">Nom</label>
                <div class="control">
                    <input class="input" id="nom" type="text" name="nom" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="style">Style</label>
                <div class="control">
                    <input class="input" id="style" type="text" name="style" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="date">Date et heure de début</label>
                <div class="control">
                    <input class="input" id="date" type="datetime-local" name="date" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="duree">Durée (minutes)</label>
                <div class="control">
                    <input class="input" id="duree" type="number" name="duree" required>
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
                <label class="label" for="video_url">URL de la vidéo</label>
                <div class="control">
                    <input class="input" type="url" id="video_url" name="video_url" placeholder="https://example.com/video.mp4">
                </div>
            </div>
            <div class="field">
                <label class="label" for="description">Description</label>
                <div class="control">
                    <textarea class="textarea" id="description" name="description"></textarea>
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
                    <button class="button is-link">Ajouter</button>
                </div>
            </div>
        </form>
</section>
<script src="resources/js/hoverImage.js"></script>
HTML;
    }


    /**
     * Ajoute un spectacle à la base de données
     * @throws Exception Si le spectacle n'a pas pu être ajouté
     * @return string Message de succès ou d'erreur
     */
    private function postAddSpectacle(): string
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
        $urlvideo = filter_var($_POST['video_url'], FILTER_SANITIZE_URL);
        if (!isset($_POST['soiree'])) {
            $soiree = null;
        } else {
            $soiree = filter_var($_POST['soiree'], FILTER_SANITIZE_NUMBER_INT);
            $soiree = $soiree === '' ? null : intval($soiree);
        }
        if (!isset($_POST['artistes'])) {
            $artistes = [];
        } else {
            $artistes = array_map('intval', $_POST['artistes']);
        }
        $images = $_POST['images'];
        try {
            // Crée un objet spectacle
            $spectacle = new Spectacle(null, $nom, new DateTime($date), $duree, $artistes, $style, new Lieu($lieu, '', '', 0, 0), $description, false,$urlvideo,$soiree);
            $repo = NRVRepository::getInstance();
            // Ajoute le spectacle à la base de données et récupère son identifiant
            $spectacle->setId($repo->addSpectacle($spectacle));
            // Associe les artistes au spectacle
            $repo->addArtistesToSpectacle($spectacle->getId(), $artistes);
            // Associe les images au spectacle
            if (isset($images)) {
                $repo->addImagesToSpectacle($spectacle->getId(), $images);
            }
            // Renvoie un message de succès
            return "<div class='notification is-success'>Spectacle ajouté avec succès</div>";
        } catch (Exception $e) {
            // Renvoie un message d'erreur
            return "<div class='notification is-danger'>Erreur lors de l'ajout du spectacle : {$e->getMessage()}</div>";
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
            return "Vous devez être connecté pour ajouter un spectacle.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postAddSpectacle();
        } else {
            return $this->getAddSpectacle();
        }
    }

}