<?php

namespace App\Core;

final class ErrorHandler
{
    private const array ERROR_TYPES = [
        E_ERROR => "ERROR",
        E_WARNING => "WARNING",
        E_PARSE => "PARSE",
        E_NOTICE => "NOTICE",
        E_CORE_ERROR => "CORE_ERROR",
        E_CORE_WARNING => "CORE_WARNING",
        E_COMPILE_ERROR => "COMPILE_ERROR",
        E_COMPILE_WARNING => "COMPILE_WARNING",
        E_USER_ERROR => "USER_ERROR",
        E_USER_WARNING => "USER_WARNING",
        E_USER_NOTICE => "USER_NOTICE",
        E_STRICT => "STRICT",
        E_RECOVERABLE_ERROR => "RECOVERABLE_ERROR",
        E_DEPRECATED => "DEPRECATED",
        E_USER_DEPRECATED => "USER_DEPRECATED",
        E_ALL => "ALL"
    ];

    public static function framework($errorLevel, $errorString, $errorFile, $errorLine): bool
    {
        // Increment errors property
        Application::app()->setProperty('errors', Application::app()->getProperty('errors') + 1);

        // Get error type
        $errorType = self::ERROR_TYPES[$errorLevel] ?? 'UNKNOWN';

        // Log
        $log = date('Y-m-d H:i:s') . "\n";
        $log .= "file: $errorFile\n";
        $log .= "line: $errorLine\n";
        $log .= "type: $errorType\n";
        $log .= "text: $errorString\n";
        $log .= "--------------------------------------------------------------------------------------------------------------------------\n";
        file_put_contents('/var/lib/photon/logs/errors.txt', $log, FILE_APPEND);

        // Show error on screen
        return false;
    }
}
