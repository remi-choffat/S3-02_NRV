<?php

namespace iutnc\nrv\action;

class UnknownAction extends Action
{
    /**
     * Affiche un message indiquant que l'action n'est pas reconnue
     * @return string
     */
    public function execute(): string
    {
        return <<<HTML
<section class='section'>
<h2 class='is-2'>Action inconnue</h2>
<p>L'action demandée n'est pas reconnue.</p>
<br/>
<button class='button' onclick='history.back()'>Retour</button>
<a class='button' href="?action=default">Accueil</a>
</section>
HTML;
    }
}