<?php
namespace iutnc\nrv\exception;
/**
 * exception lancée lorsqu'un spectacle n'est pas compatible avec le lieu de la soirée
 */
class LieuIncompatibleException extends \Exception {
    /**
     * constructeur
     * @param string $message
     */
    public function __construct(){
        parent::__construct("Le lieu du spectacle doit être le même que celui de la soirée");
    }
}