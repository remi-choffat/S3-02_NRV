<?php
namespace iutnc\nrv\action;
use iutnc\nrv\action\Action;
use iutnc\nrv\repository\NRVRepository;
use Exception;
/**
 * classe AjouterImages dans la base de données
 */
class AjouterImageAction extends Action {
    /**
     * Retourne le formulaire d'ajout d'un lieu
     * @return string Formulaire d'ajout d'un lieu
     */
    private function getAddImages(): string
    {
        return <<<HTML
    <section class="section">
        <form action="index.php?action=ajouter-image" method="POST" enctype="multipart/form-data">
        <h1 class="title">Ajouter une Image</h1>
            <br/>
            <div class="field">
                <div class="control">
                    <label for="fileName">Nom de la photo</label>
                    <input type="text" id="fileName" name="file_name" value="FileName" required>
                </div>
                <div class="control">
                    <input type="hidden" name="MAX_FILE_SIZE" value="50000000">
                    <label for="image">photo</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
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
     * Ajoute un lieu à la base de données
     * @throws Exception Si le lieu n'a pas pu être ajouté
     * @return string Message de succès ou d'erreur
     */
    private function postAddImage(): string
    {
        if (!isset($_POST['file_name'], $_FILES['image'])) {
            return "<div class='notification is-warning'>Tous les champs obligatoires ne sont pas remplis</div>";
        }
        //filtre les données
        $filename = filter_var($_POST['file_name'], FILTER_SANITIZE_SPECIAL_CHARS);
        //validation du fichier: vérifie si le fichier est une image et si son extension est valide
        if (in_array($_FILES['image']['type'], ['image/jpeg', 'image/png', 'image/gif']) &&
            preg_match('/\.(gif|png|jpg)$/i', $_FILES['image']['name'])) {
                // si fichier valide alors upload
                $uploaddir = 'images/';
                $uploadfile = $uploaddir . $filename . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile);
                // ajoute le nom de l'image dans la base de données
                try {
                    // verifie si l'image n'est pas déjà dans la base de données
                    $repo = NRVRepository::getInstance();
                    $images = $repo->getImages();
                    if(in_array($uploadfile, $images)){
                        return "<div class='notification is-danger'>Ce nom a déjà utilisé</div>";
                    }
                    // Ajoute le nom de l'image dans la base de données
                    $repo->UploadImage($uploadfile);
                    // Renvoie un message de succès
                    return "<div class='notification is-success'>Image ajouté avec succès</div>";
                } catch (Exception $e) {
                    // Renvoie un message d'erreur
                    return "<div class='notification is-danger'>Erreur lors de l'ajout de l'image : {$e->getMessage()}</div>";
                }
            }
        else{
            return " <div class='notification is-danger'>potentielle attaque lors de l'upload de l'image</div>";
        }
    }
    /**
     * Exécute l'action en fonction de la méthode HTTP
     * @throws Exception
     */
    public function execute(): string
    {
        if (!isset($_SESSION['utilisateur'])) {
            return "Vous devez être connecté pour ajouter un lieu.";
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->postAddImage();
        } else {
            return $this->getAddImages();
        }
    }
}