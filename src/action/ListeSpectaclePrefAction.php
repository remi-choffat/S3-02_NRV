<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;

class ListeSpectaclePrefAction extends Action
{

    public function execute(): string
    {
        $html = "<h2 class='subtitle'>Vos spectacles préférés</h2>";
        $spectacles = NRVRepository::getInstance()->getSpectaclePref();
        foreach ($spectacles as $spectacle) {
            $html .= "<article class='tile is-child box article-item'>
                <h3 class='title is-3'><a href='?action=details-spectacle&id={$spectacle['id']}'>{$spectacle['titre']}</a></h3>
                <p><b>Date :</b> {$spectacle['date']}</p>
                <p><b>Heure :</b> {$spectacle['horaire']}</p>
                </article>";
        }

        return $html;
    }
}