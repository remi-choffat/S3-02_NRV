<?php
/**
 * Exception levée lors d'une erreur d'inscription
 */
namespace iutnc\nrv\exception;
use Exception;
class InscriptionException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}