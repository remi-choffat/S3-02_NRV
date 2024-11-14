<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

/**
 * Action permettant de restaurer un spectacle annulé
 */
class RestaurerSpectacleAction extends Action
{

    public function execute(): string
    {
        try {
            $idSpectacle = $_GET['id'];
            $repository = NRVRepository::getInstance();
            $spectacle = $repository->getSpectacle($idSpectacle);

            $spectacle->setAnnule(false);

            $repository->updateSpectacle($spectacle);

            return <<<HTML
                <section class='section'>
                    <div class='notification is-success'>Le spectacle a bien été restauré</div>
                    <br/>
                    <a href='index.php?action=details-spectacle&id=$idSpectacle' class='button'>Voir le spectacle</a>
                    <a href='index.php?action=liste-spectacles' class='button'>Retour à la liste</a>
                </section>
            HTML;
        } catch (\Exception $e) {
            return <<<HTML
                <section class='section'>
                    <div class='notification is-danger'>Une erreur est survenue lors de la restauration du spectacle</div>
                    <p class='content'>{$e->getMessage()}</p>
                    <br/>
                    <a href='index.php?action=liste-spectacles' class='button'>Retour à la liste</a>
                </section>
            HTML;
        }
    }

}