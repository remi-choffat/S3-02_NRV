<?php

namespace iutnc\nrv\exception;

/**
 * Exception lancée lorsqu'un spectacle n'est pas compatible avec le lieu de la soirée
 */
class LieuIncompatibleException extends \Exception
{
    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct("Le lieu du spectacle doit être le même que celui de la soirée");
    }
}