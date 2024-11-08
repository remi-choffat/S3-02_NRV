<?php

namespace iutnc\nrv\exception;

/**
 * Exception lancée lorsqu'un spectacle n'est pas compatible avec la date de la soirée
 */
class DateIncompatibleException extends \Exception
{
    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct("La date du spectacle doit être la même que celle de la soirée");
    }
}