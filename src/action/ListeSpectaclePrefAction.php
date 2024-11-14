<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

/**
 * Action pour afficher la liste des spectacles préférés de l'utilisateur (non authentifié)
 */
class ListeSpectaclePrefAction extends Action
{

    /**
     * @throws DateMalformedStringException
     */
    public function execute(): string
    {
        $html = "<h2 class='subtitle'>Vos spectacles préférés</h2>";

        if (empty($_SESSION["favoris"])) {
            $html .= "<div class='notification is-info'>Vous n'avez pas de spectacle préféré</div>";
            return $html;
        }

        $favoris = $_SESSION["favoris"];
        $repository = NRVRepository::getInstance();

        foreach ($favoris as $id) {
            $spectacle = $repository->getSpectacle(intval($id));
            $html .= $spectacle->afficherResume();
        }

        $html .= "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.star').forEach(function(star) {
                star.addEventListener('click', function() {
                    this.classList.toggle('filled');
                    this.classList.toggle('empty');
                    let spectacleId = this.getAttribute('data-id');
                    fetch('index.php?action=' + (this.classList.contains('filled') ? 'ajouter-pref' : 'supprimer-pref'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'spectacle=' + encodeURIComponent(spectacleId)
            })
            .then(response => response.text())
            .then(data => {
                // Supprime le spectacle de la page
                if (this.classList.contains('empty')) {
                    this.parentElement.parentElement.parentElement.style.display = 'none';            
                }
            })
            .catch((error) => {
                console.error('Erreur :', error);
            });
                });
            });
        });
        </script>";

        return $html;
    }

}