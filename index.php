<?php

declare(strict_types=1);

use iutnc\nrv\dispatch\Dispatcher;

require_once 'vendor/autoload.php';
session_start();

// Crée un dispatcheur et exécute l'action
$action = $_GET['action'] ?? 'null';
$dispatcher = new Dispatcher($action);
$dispatcher->run();