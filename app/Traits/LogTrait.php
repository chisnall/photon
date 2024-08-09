<?php

declare(strict_types=1);

namespace App\Traits;

trait LogTrait
{
    private function log(string $file, array $entries): void
    {
        // Check for debug mode
        if (!APP_DEBUG) {
            return;
        }

        // Get backtrace
        $debugBacktrace = debug_backtrace();
        $traceInfoFile = basename($debugBacktrace[0]['file']);
        $traceInfoPath = $debugBacktrace[0]['file'];
        $traceInfoLine = $debugBacktrace[0]['line'];

        // Log
        $log = date('Y-m-d H:i:s') . "\n\n";
        $log .= "file: $traceInfoFile\n";
        //$log .= "path: $traceInfoPath\n";
        $log .= "line: $traceInfoLine\n\n";
        foreach ($entries as $entry) {
            $log .= "$entry\n";
        }
        $log .= "-------------------------------------------------------------------------------------------------------------------\n";
        file_put_contents("/var/lib/photon/logs/$file.txt", $log, FILE_APPEND);
    }
}
