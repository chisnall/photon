<?php

namespace App\Core;

class Functions
{
    public static function snakeToCamel(string $string): string
    {
        // Check for underscore character
        if (str_contains($string, '_')) {
            return lcfirst(str_replace('_', '', ucwords(strtolower($string), '_')));
        }

        // Return existing string - this means we can run the method again on an existing camelCase string
        return $string;
    }

    public static function camelToSnake(string $string): string
    {
        // Replace uppercase character with underscore and lowercase the whole string
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function dotToCamel(string $string): string
    {
        // Check for dot character
        if (str_contains($string, '.')) {
            return lcfirst(str_replace('.', '', ucwords(strtolower($string), '.')));
        }

        // Return existing string - this means we can run the method again on an existing camelCase string
        return $string;
    }

    public static function checkConfig($key): mixed
    {
        // !!! do not change the {{value}} style output here to returning "null" or "false"
        // that will break config keys which actually use "null" or "false" for values

        // Get config
        $config = Application::app()->getProperty('config');

        // Turn key into array
        $keyArray = explode('/', $key);

        // Process keys
        foreach ($keyArray as $keyItem) {
            // Confirm array key exists
            if (array_key_exists($keyItem, $config)) {
                // Update config variable
                $config = $config[$keyItem];

                // Check for empty or null
                if ($config === '' || $config === null) {
                    return '{{null}}';
                }
            } else {
                return '{{missing}}';
            }
        }

        // Return
        return $config;
    }

    public static function getConfig($key, $missing = false): mixed
    {
        // Check config
        $config = self::checkConfig($key);

        // Check for missing
        if ($config === '{{missing}}') {
            if ($missing === true) {
                return null;
            }
            throw new (self::getConfig("class/exception/framework"))(message: "Config is missing: $key");
        }

        // Check for null
        if ($config === '{{null}}') {
            if ($missing === true) {
                return null;
            }
            throw new (self::getConfig("class/exception/framework"))(message: "Config is null: $key");
        }

        // Return
        return $config;
    }

    public static function includeFile(string $file, ?string $message = null, ?array $variables = null, $once = false): mixed
    {
        // Set default message
        if (!$message) {
            $message = "File is missing: $file";
        }

        // Set full path
        $filePath = BASE_PATH . $file;

        // Check path exists
        if (!file_exists($filePath)) {
            throw new (Functions::getConfig("class/exception/framework"))(message: $message);
        }

        // Process variables so they are available to the include
        if ($variables) {
            foreach ($variables as $variableKey => $variableValue) {
                $$variableKey = $variableValue;
            }

            // Remove variables
            unset($file);
            unset($message);
            unset($variables);
            unset($variableValue);
            unset($variableKey);
        }

        // Return include
        if ($once) {
            return include_once $filePath;
        } else {
            return include $filePath;
        }
    }

    public static function deleteFile(string $file): void
    {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function traceInfo(string $position = "end"): array
    {
        // Get backtrace
        $debugBacktrace = debug_backtrace();

        // Check for start argument
        if ( $position == "start" ) {
            rsort($debugBacktrace);
        }

        // Set return array
        $debugBacktraceInfo = [];
        $debugBacktraceInfo['file'] = basename($debugBacktrace[0]['file']);
        $debugBacktraceInfo['path'] = $debugBacktrace[0]['file'];
        $debugBacktraceInfo['line'] = $debugBacktrace[0]['line'];

        // Return
        return $debugBacktraceInfo;
    }

    public static function cleanupSessions(): void
    {
        // Get web server user ID
        $httpUserId = posix_getuid();

        // Set current time
        $timeNow = time();

        // Age for deletion
        $fileDeleteAge = 86400; // 24 hours

        // Get session save path
        $sessionSavePath = session_save_path() ?: '/tmp';

        // Get files
        $files = glob("$sessionSavePath/sess_*");

        foreach ($files as $filePath) {
            // Get file mtime
            $fileAge = $timeNow - filemtime($filePath);

            // Get file owner
            $fileOwnerId = fileowner($filePath);

            // Delete old files - we can only delete files that are owned by the web server user
            if ($fileAge > $fileDeleteAge && $fileOwnerId == $httpUserId) {
                self::deleteFile($filePath);
            }
        }
    }

    public static function time(): void
    {
        // Calculate application time
        $appTotalTime = number_format((microtime(true) - APP_START) * 1000);

        // Update footer
        echo "<script>$('div#app-time').html('$appTotalTime ms');</script>\n";
    }
}
