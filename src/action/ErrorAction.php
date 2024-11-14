<?php

namespace iutnc\nrv\action;

use AllowDynamicProperties;
use iutnc\nrv\exception\UnauthorizedActionException;

class ErrorAction extends Action
{

    /**
     * Affiche un message indiquant l'erreur
     * @return string
     */
    public function execute(): string
    {
        // Récupère le message d'erreur
        if (isset($_GET['message'])) {
            $message = urldecode($_GET['message']);
        } else {
            $message = '';
        }

        return <<<HTML
        <section class='section'>
            <h2 class='is-2'>Une erreur est survenue</h2>
            <br/>
            <div class='notification is-danger'>
                <p>{$message}</p>
            </div>
            <br/>
            <button class='button' onclick='history.back()'>Retour</button>
        </section>
HTML;
    }
}