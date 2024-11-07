<?php

declare(strict_types=1);

use iutnc\nrv\dispatch\Dispatcher;
use iutnc\nrv\repository\NRVRepository;

require_once 'vendor/autoload.php';
session_start();

NRVRepository::setConfig('UN_FICHIER_DE_CONFIG');

// Crée un dispatcheur et exécute l'action
$action = $_GET['action'] ?? 'null';
$dispatcher = new Dispatcher($action);
$dispatcher->run();