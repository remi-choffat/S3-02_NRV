<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

/**
 * Action permettant d'annuler un spectacle
 */
class AnnulerSpectacleAction extends Action
{

    public function execute(): string
    {
        try {
            $idSpectacle = $_GET['id'];
            $repository = NRVRepository::getInstance();
            $spectacle = $repository->getSpectacle($idSpectacle);

            $spectacle->setAnnule(true);

            $repository->modifierAnnulationSpectacle($spectacle->getId(), true);

            return <<<HTML
                <section class='section'>
                    <div class='notification is-success'>Le spectacle a bien été annulé</div>
                    <br/>
                    <a href='index.php?action=details-spectacle&id=$idSpectacle' class='button'>Voir le spectacle annulé</a>
                    <a href='index.php?action=liste-spectacles' class='button'>Retour à la liste</a>
                </section>
            HTML;
        } catch (\Exception $e) {
            return <<<HTML
                <section class='section'>
                    <div class='notification is-danger'>Une erreur est survenue lors de l'annulation du spectacle</div>
                    <p class='content'>{$e->getMessage()}</p>
                    <br/>
                    <a href='index.php?action=liste-spectacles' class='button'>Retour à la liste</a>
                </section>
            HTML;
        }
    }

}