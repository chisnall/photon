<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class Logger
{
    private string $filename;
    private array $entry;
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

    public function logException(Throwable $exception): void
    {
        // Set log filename
        $this->filename = 'exceptions.log';

        // Init entry
        $this->entry = [];

        // Get exception details
        $exceptionDetails = new ExceptionDetails($exception);
        $shortClassName = $exceptionDetails->shortClassName;
        $exceptionMessage = $exceptionDetails->message;
        $exceptionPreviousMessage = $exceptionDetails->previousMessage;
        $exceptionCode = $exceptionDetails->code;
        $exceptionFile = $exceptionDetails->file;
        $exceptionLine = $exceptionDetails->line;
        $exceptionTraceString = $exceptionDetails->traceString;

        // Build entry
        $this->entry[] = "$shortClassName\n";
        if ($exceptionPreviousMessage) {
            $this->entry[] = "mess: $exceptionPreviousMessage";
        } else {
            $this->entry[] = "mess: $exceptionMessage";
        }
        $this->entry[] = "code: $exceptionCode";
        $this->entry[] = "file: $exceptionFile";
        $this->entry[] = "line: $exceptionLine\n";
        $this->entry[] = "$exceptionTraceString";

        // Write log
        $this->writeLog();
    }

    public function logError(int $errorLevel, string $errorString, string $errorFile, int $errorLine): void
    {
        // Set log filename
        $this->filename = 'errors.log';

        // Init entry
        $this->entry = [];

        // Get error type
        $errorType = self::ERROR_TYPES[$errorLevel] ?? 'UNKNOWN';

        // Build entry
        $this->entry[] = "file: $errorFile";
        $this->entry[] = "line: $errorLine";
        $this->entry[] = "type: $errorType";
        $this->entry[] = "text: $errorString";

        // Write log
        $this->writeLog();
    }

    public function logDebug(string $filename, array $entry): void
    {
        // Check for debug mode
        if (!APP_DEBUG) {
            return;
        }

        // Set log filename
        $this->filename = $filename;

        // Init entry
        $this->entry = [];

        // Get backtrace
        $debugBacktrace = debug_backtrace();
        $traceInfoFile = basename($debugBacktrace[0]['file']);
        //$traceInfoPath = $debugBacktrace[0]['file'];
        $traceInfoLine = $debugBacktrace[0]['line'];

        // Build entry
        $this->entry[] = "file: $traceInfoFile";
        //$this->entry[] = "path: $traceInfoPath";
        $this->entry[] = "line: $traceInfoLine\n";
        $this->entry = array_merge($this->entry, $entry);

        // Write log
        $this->writeLog();
    }

    private function writeLog(): void
    {
        // Build entry
        $entry = date('Y-m-d H:i:s') . "\n\n";
        foreach ($this->entry as $entryLine) {
            $entry .= "$entryLine\n";
        }
        $entry .= "--------------------------------------------------------------------------------------------------------------------------------------------------------------\n";

        // Write log
        file_put_contents('/var/lib/photon/logs/' . $this->filename, $entry, FILE_APPEND);
    }
}
