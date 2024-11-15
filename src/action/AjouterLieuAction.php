<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use DateTime;
use Exception;
use iutnc\nrv\festival\Lieu;
use iutnc\nrv\festival\Soiree;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action d'ajout d'un lieu
 */
class AjouterLieuAction extends Action
{
    /**
     * Retourne le formulaire d'ajout d'un lieu
     * @return string Formulaire d'ajout d'un lieu
     */
    private function getAddLieu(): string
    {
        $repo = NRVRepository::getInstance();
        $images = $repo->getImages();
        $imageOptions = "";
        foreach ($images as $image) {
            $imageOptions .= "<option value='$image' data-image='resources/images/$image'>$image</option>";
        }
        return <<<HTML
    <section class="section">
        <h1 class="title">Ajouter un lieu</h1>
        <form action="index.php?action=ajouter-lieu" method="POST">
            <div class="field">
                <label class="label required" for="nom">Nom</label>
                <div class="control">
                    <input class="input" id="nom" type="text" name="nom" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="adresse">Adresse</label>
                <div class="control">
                    <input class="input" id="adresse" type="text" name="adresse">
                </div>
            </div>
            <div class="field">
                <label class="label required" for="date">Nombre de places debout</label>
                <div class="control">
                    <input class="input" id="nbpldeb" type="number" name="nbpldeb" required>
                </div>
            </div>
            <div class="field">
                <label class="label required" for="date">Nombre de places assises</label>
                <div class="control">
                    <input class="input" id="nbplass" type="number" name="nbplass" required>
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
     * Ajoute un lieu à la base de données
     * @throws Exception Si le lieu n'a pas pu être ajouté
     * @return string Message de succès ou d'erreur
     */
    private function postAddLieu(): string
    {
        if (!isset($_POST['nom'], $_POST['adresse'], $_POST['nbpldeb'], $_POST['nbplass'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }

        $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
        $adresse = filter_var($_POST['adresse'], FILTER_SANITIZE_SPECIAL_CHARS);
        $nbpldeb = filter_var($_POST['nbpldeb'], FILTER_SANITIZE_NUMBER_INT);
        $nbplass = filter_var($_POST['nbplass'], FILTER_SANITIZE_NUMBER_INT);
        $images = $_POST['images'];
        try {
            // Crée un objet lieu
            $lieu = new Lieu(null, $nom, $adresse, $nbpldeb, $nbplass);
            $repo = NRVRepository::getInstance();
            // Ajoute le lieu à la base de données et récupère son identifiant
            $lieu->setId($repo->addLieu($lieu));
            // ajoute les images du lieu
            $repo->addImageToLieu($lieu->getId(), $images);
            // Renvoie un message de succès
            return "<div class='notification is-success'>Lieu ajouté avec succès</div>";
        } catch (Exception $e) {
            // Renvoie un message d'erreur
            return "<div class='notification is-danger'>Erreur lors de l'ajout du lieu : {$e->getMessage()}</div>";
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
            return "Vous devez être connecté pour ajouter un lieu.";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postAddLieu();
        } else {
            return $this->getAddLieu();
        }
    }

}