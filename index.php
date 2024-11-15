<?php

declare(strict_types=1);

use iutnc\nrv\dispatch\Dispatcher;
use iutnc\nrv\exception\ExceptionHandler;
use iutnc\nrv\repository\NRVRepository;

require_once 'vendor/autoload.php';
session_start();

NRVRepository::setConfig('config.db.ini');

set_exception_handler([ExceptionHandler::class, 'handleException']);

// Crée un dispatcheur et exécute l'action
$action = $_GET['action'] ?? 'null';
$dispatcher = new Dispatcher($action);
try {
    $dispatcher->run();
} catch (Exception $e) {
    ExceptionHandler::handleException($e);
}