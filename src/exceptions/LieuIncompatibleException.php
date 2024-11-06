<?php
namespace iutnc\nrv\exception;
/**
 * exception thrown when an invalid property name is used
 */
class LieuIncompatibleException extends \Exception {
    /**
     * constructor of the class
     * @param string $message
     */
    public function __construct(){
        parent::__construct("Le lieu du spectacle doit être le même que celui de la soirée");
    }
}