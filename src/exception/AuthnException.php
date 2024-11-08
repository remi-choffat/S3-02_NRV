<?php

namespace iutnc\nrv\exception;

use Exception;

class AuthnException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}