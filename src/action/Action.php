<?php

namespace iutnc\nrv\action;

use iutnc\nrv\exception\UnauthorizedActionException;

abstract class Action
{

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    protected ?int $restriction;

    /**
     * @param int|null $role role minimum nécessaire pour que l'action s'éxécute (0 est la plus grande restriction)
     * @throws UnauthorizedActionException
     */
    public function __construct(int $role = null)
    {
        $this->restriction = $role;
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
        $this->canExecute();
    }

    abstract public function execute(): string;

    /**
     *
     * @return void
     * @throws UnauthorizedActionException lance une exception s'il faut être connecter ou si le role de l'utilisateur n'est pas suffisant
     */
    public function canExecute(): void
    {
        if (!is_null($this->restriction)) {
            if (isset($_SESSION['utilisateur'])) {
                $user = unserialize($_SESSION['utilisateur']);
                if ($user->getRole() > $this->restriction) {
                    if ($this->restriction == 0) {
                        $role = "admin";
                    } else {
                        $role = "staff";
                    }
                    throw new UnauthorizedActionException("Vous devez être un " . $role . " pour effectuer cette action");
                }
            } else {
                throw new UnauthorizedActionException("Vous devez être connecté pour effectuer cette action");
            }
        }
    }

}
