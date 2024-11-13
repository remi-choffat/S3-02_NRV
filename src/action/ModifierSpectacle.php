<?php

namespace iutnc\nrv\action;

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\exception\SpectacleAssignationException;

class ModifierSpectacle extends Action {

    public function execute(): string
    {
        $html="";
        if ($this->http_method=="GET"){

        }
        return $html;
    }
}