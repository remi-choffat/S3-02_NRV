<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

class DetailsSpectacleAction extends Action
{
    /**
     * Affiche les détails d'un spectacle
     * @return string
     * @throws DateMalformedStringException
     */
    public function execute(): string
    {
        $idSpectacle = $_GET['id'];
        $repository = NRVRepository::getInstance();
        $spectacle = $repository->getSpectacle($idSpectacle);

        $similarSpectacles = $repository->getSimilarSpectacles(
            $spectacle->getId(),
            $spectacle->getStyle(),
            $spectacle->getLieu()->getId(),
            $spectacle->getDate()
        );

        $html = $spectacle->afficherDetails();

        if (!empty($similarSpectacles)) {

            // Crée 3 listes de spectacles similaires : par lieu, par date, par style
            $similarSpectaclesByLieu = [];
            $similarSpectaclesByDate = [];
            $similarSpectaclesByStyle = [];
            $displayedSpectacleIds = [];

            foreach ($similarSpectacles as $similarSpectacle) {
                if ($similarSpectacle->getLieu()->getId() === $spectacle->getLieu()->getId()) {
                    $similarSpectaclesByLieu[] = $similarSpectacle;
                }
                if ($similarSpectacle->getDate()->format('Y-m-d') === $spectacle->getDate()->format('Y-m-d')) {
                    $similarSpectaclesByDate[] = $similarSpectacle;
                }
                if ($similarSpectacle->getStyle() === $spectacle->getStyle()) {
                    $similarSpectaclesByStyle[] = $similarSpectacle;
                }
            }

            $html .= "<h2 class='subtitle titre-similaires'>Spectacles similaires</h2><div class='similar-spectacles'>";

            // Affiche un spectacle similaire par lieu
            foreach ($similarSpectaclesByLieu as $similarSpectacle) {
                if (!in_array($similarSpectacle->getId(), $displayedSpectacleIds) && !$similarSpectacle->isAnnule()) {
                    $html .= $similarSpectacle->afficherResumeCompact("Même lieu");
                    $displayedSpectacleIds[] = $similarSpectacle->getId();
                    break;
                }
            }

            // Affiche un spectacle similaire par date
            foreach ($similarSpectaclesByDate as $similarSpectacle) {
                if (!in_array($similarSpectacle->getId(), $displayedSpectacleIds) && !$similarSpectacle->isAnnule()) {
                    $html .= $similarSpectacle->afficherResumeCompact("Même jour");
                    $displayedSpectacleIds[] = $similarSpectacle->getId();
                    break;
                }
            }

            // Affiche un spectacle similaire par style
            foreach ($similarSpectaclesByStyle as $similarSpectacle) {
                if (!in_array($similarSpectacle->getId(), $displayedSpectacleIds) && !$similarSpectacle->isAnnule()) {
                    $html .= $similarSpectacle->afficherResumeCompact("Même style");
                    break;
                }
            }

            if (empty($displayedSpectacleIds)) {
                $html .= "<div class='box'>Aucun spectacle similaire n'est maintenu dans la programmation</div>";
            }

            $html .= "</div>";
        }

        // Si le spectacle appartient à une soirée, on affiche la soirée
        if ($spectacle->getSoireeId() !== null) {
            $soiree = $repository->getSoiree($spectacle->getSoireeId());
            $html .= "<h2 class='subtitle'>Soirée</h2>";
            $html .= $soiree->afficherResume();
        }

        return $html;
    }
}