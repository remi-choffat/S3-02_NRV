<?php
/**
 * Exception levée lorsqu'un utilisateur n'a pas les droits suffisants pour accéder à une ressource.
 */
namespace iutnc\nrv\exception;
use Exception;
class AccessControlException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}