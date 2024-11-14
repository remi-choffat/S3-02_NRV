<?php

namespace iutnc\nrv\exception;

use JetBrains\PhpStorm\NoReturn;

class ExceptionHandler
{
    #[NoReturn] public static function handleException(\Throwable $exception): void
    {
        // Log les détails de l'erreur
        error_log($exception->getMessage());
        error_log($exception->getTraceAsString());

        // Redirige vers la page d'erreur
        header('Location: index.php?action=error&message=' . urlencode($exception->getMessage()));
        exit();

    }
}