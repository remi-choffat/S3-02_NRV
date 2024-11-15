<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use Exception;

/**
 * Action d'ajout d'une image
 */
class AjouterImageAction extends Action
{

    /**
     * Retourne le formulaire d'ajout d'une image
     * @return string Formulaire d'ajout d'une image
     */
    private function getAddImages(): string
    {
        return <<<HTML
    <section class="section">
        <h1 class="title">Ajouter une Image</h1>
        <form action="index.php?action=ajouter-image" method="POST" enctype="multipart/form-data">
            <div class="field">
               <label class="label required" for="fileName">Nom de l'image</label>
                <div class="control">
                    <input class="input" type="text" id="fileName" name="file_name" required>
                </div>
            </div>
            <div class="file">
              <label class="file-label required" for="image">
                <input id="image" class="file-input" type="file" name="image" onchange="updateFileName()" />
                <span class="file-cta">
                  <span class="file-icon">
                    <i class="fa fa-upload"></i>
                  </span>
                  <span class="file-label" id="file-label"> Choisir un fichier… </span>
                </span>
              </label>
            </div>
            <br/>
            <div class="field">
                <div class="control">
                    <button class="button is-link">Ajouter</button>
                </div>
            </div>
        </form>
        <script>
            function updateFileName() {
                const input = document.getElementById('image');
                const label = document.getElementById('file-label');
                label.textContent = input.files[0].name;
            }
        </script>
    </section>
    HTML;
    }


    /**
     * Stocke l'image sur le serveur, et ajoute un lien vers l'image dans la base de données
     * @return string Message de succès ou d'erreur
     * @throws Exception Si l'image n'est pas valide
     */
    private function postAddImage(): string
    {
        if (!isset($_POST['file_name'], $_FILES['image'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }
        //filtre les données
        $filename = filter_var($_POST['file_name'], FILTER_SANITIZE_SPECIAL_CHARS) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        //validation du fichier : vérifie si le fichier est une image et si son extension est valide
        if (in_array($_FILES['image']['type'], ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']) &&
            preg_match('/\.(gif|png|jpg)$/i', $_FILES['image']['name'])) {

            // Si fichier valide alors upload
            $uploaddir = 'images/';

            // Si le dossier n'existe pas, le crée
            if (!file_exists($uploaddir)) {
                mkdir($uploaddir);
            }

            $uploadfile = $uploaddir . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);

            // Ajoute le nom de l'image dans la base de données
            try {
                // verifie si l'image n'est pas déjà dans la base de données
                $repo = NRVRepository::getInstance();
                $images = $repo->getImages();
                if (in_array($filename, $images)) {
                    return "<div class='notification is-warning'>Ce nom a déjà utilisé</div>";
                }
                // Ajoute le nom de l'image dans la base de données
                $repo->UploadImage($filename);
                // Renvoie un message de succès
                return "<div class='notification is-success'>Image ajoutée avec succès</div>";
            } catch (Exception $e) {
                // Renvoie un message d'erreur
                return "<div class='notification is-danger'>Erreur lors de l'ajout de l'image : {$e->getMessage()}</div>";
            }
        } else {
            return "<div class='notification is-danger'>La validité de l'image importée n'a pas été vérifiée</div>";
        }
    }


    /**
     * Exécute l'action en fonction de la méthode HTTP
     * @throws Exception
     */
    public function execute(): string
    {
        if (!isset($_SESSION['utilisateur'])) {
            return "Vous devez être connecté pour ajouter une image.";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postAddImage();
        } else {
            return $this->getAddImages();
        }
    }
}