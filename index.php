<?php

declare(strict_types=1);

use iutnc\nrv\dispatch\Dispatcher;
use iutnc\nrv\repository\NRVRepository;

require_once 'vendor/autoload.php';
session_start();

NRVRepository::setConfig('config.db.ini');

// Crée un dispatcheur et exécute l'action
$action = $_GET['action'] ?? 'null';
$dispatcher = new Dispatcher($action);
$dispatcher->run();

var_dump(NRVRepository::getInstance()->getSpectacles());