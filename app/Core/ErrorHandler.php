<?php

declare(strict_types=1);

namespace App\Core;

final class ErrorHandler
{
    public static function framework($errorLevel, $errorString, $errorFile, $errorLine): bool
    {
        // Increment errors property
        Application::app()->setProperty('errors', Application::app()->getProperty('errors') + 1);

        // Log
        Application::app()->logger()->logError($errorLevel, $errorString, $errorFile, $errorLine);

        // Output error so we can view on screen
        return false;
    }

    public static function ajax($errorLevel, $errorString, $errorFile, $errorLine): bool
    {
        // Log
        Application::app()->logger()->logError($errorLevel, $errorString, $errorFile, $errorLine);

        // Don't output error
        return true;
    }
}
