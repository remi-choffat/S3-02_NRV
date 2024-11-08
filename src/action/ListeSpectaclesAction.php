<?php

namespace iutnc\nrv\action;

use DateMalformedStringException;
use iutnc\nrv\repository\NRVRepository;

class ListeSpectaclesAction extends Action
{
    /**
     * Liste les spectacles du festival
     * @return string affichage des spectacles
     * @throws DateMalformedStringException
     */
    public function execute(): string
    {
        $repository = NRVRepository::getInstance();
        $styles = $repository->getStyles();
        $lieux = $repository->getLieux();

        $selectedStyles = array_map('urldecode', $_GET['styles'] ?? []);
        $selectedLieux = array_map('urldecode', $_GET['lieux'] ?? []);
        $dateStart = $_GET['date_start'] ?? null;
        $dateEnd = $_GET['date_end'] ?? null;

        $spectacles = $repository->getSpectacles($selectedStyles, $selectedLieux, $dateStart, $dateEnd);

        $html = "<h2 class='subtitle'>Liste des spectacles du festival</h2>";

// Ajouter l'icône de filtrage avec espacement
        $html .= "<div class='filter-icon' onclick='toggleFilterForm()' style='margin-bottom: 10px; cursor: pointer;'>
            <span class='fa fa-filter'></span> <span>Filtrer</span>
          </div>";

// Ajouter le formulaire de filtres
        $html .= "<form id='filter-form' method='GET' action='?action=liste-spectacles' style='display: none;'>
            <input type='hidden' name='action' value='liste-spectacles'>
            <div class='filter-container'>
                <div class='filter-column'>
                <label class='label'>Styles</label>";
        foreach ($styles as $style) {
            $encodedStyle = htmlspecialchars($style, ENT_QUOTES, 'UTF-8');
            $html .= "<label>
                <input type='checkbox' name='styles[]' value='$encodedStyle'>
                $style
              </label>";
        }
        $html .= "</div>
          <div class='filter-column'>
              <label class='label'>Lieux</label>";
        foreach ($lieux as $lieu) {
            $encodedLieu = htmlspecialchars($lieu->getId(), ENT_QUOTES, 'UTF-8');
            $html .= "<label>
                <input type='checkbox' name='lieux[]' value='$encodedLieu'>
                {$lieu->getNom()}
              </label>";
        }
        $html .= "</div>
          <div class='filter-column'>
              <label class='label'>Date</label>
              <label>
                  Du : <input type='datetime-local' name='date_start'>
              </label>
              <label>
                  Au : <input type='datetime-local' name='date_end'>
              </label>
          </div>
          </div>
          <button type='submit'>Filtrer</button>
        </form>
        <script>
function toggleFilterForm() {
    let form = document.getElementById('filter-form');
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>";

        if (empty($spectacles)) {
            $html .= "<div class='notification is-warning' style='margin: 20px;'>Aucun spectacle ne correspond à vos critères de recherche</div>";
        } else {
            foreach ($spectacles as $spectacle) {
                $html .= $spectacle->afficherResume();
            }
        }

        return $html;
    }

}