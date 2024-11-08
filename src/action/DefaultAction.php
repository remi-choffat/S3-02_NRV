<?php

namespace iutnc\nrv\action;

class DefaultAction extends Action
{
    /**
     * Affiche un message de bienvenue
     * @return string
     */
    public function execute(): string
    {
        $message = "Bienvenue !";
        return "<h2 class='subtitle'>$message</h2>";
    }
}