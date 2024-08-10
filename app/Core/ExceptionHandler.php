<?php

declare(strict_types=1);

namespace App\Core;

use Exception;
use ReflectionClass;
use Throwable;

final class ExceptionHandler
{
    private const array HTTP_CODES = [
        400 => 'Bad Request',
        401 => 'Unauthorised',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    public static function framework(Throwable $exception): never
    {
        // Sets the secondary exception handler to handle exceptions in *this* class
        // This will happen if the Application class cannot instantiate
        set_exception_handler([ExceptionHandler::class, 'frameworkSecondary']);

        // Set status code
        http_response_code(500);

        // Check for missing config file
        // Using code 10 for this - and need to throw standard Exception
        if ($exception->getCode() == 10) {
            throw new Exception(message: $exception->getMessage());
        }

        // Set layout config key
        $layoutKey = 'page/error/framework/layout';
        $viewKey = 'page/error/framework/view';

        // Get layout value
        $layoutFile = Functions::getConfig($layoutKey, true);
        if (!$layoutFile) {
            throw new Exception(message: "Error layout configuration is missing: $layoutKey");
        } else {
            // Check layout file is present
            $layoutPath = BASE_PATH . "/app/Views/layouts/$layoutFile.php";
            if (!file_exists($layoutPath)) {
                throw new Exception(message: "Error layout file is missing: $layoutPath");
            }
        }

        // Set view config key
        $viewFile = Functions::getConfig($viewKey, true);
        if (!$viewFile) {
            throw new Exception(message: "View layout configuration is missing: $viewKey");
        } else {
            // Check view file is present
            $viewPath = BASE_PATH . "/app/Views/$viewFile.php";
            if (!file_exists($viewPath)) {
                throw new Exception(message: "Error view file is missing: $viewPath");
            }
        }

        // Set title
        $title = 'Error';

        // Get exception details
        $exceptionDetails = new ExceptionDetails($exception);
        $className = $exceptionDetails->className;
        $shortClassName = $exceptionDetails->shortClassName;
        $exceptionMessage = $exceptionDetails->message;
        $exceptionPreviousMessage = $exceptionDetails->previousMessage;
        $exceptionCode = $exceptionDetails->code;
        $exceptionFile = $exceptionDetails->file;
        $exceptionLine = $exceptionDetails->line;
        $exceptionTrace = $exceptionDetails->trace;
        $exceptionTraceString = $exceptionDetails->traceString;

        // Check for certain class names
        if ($className == 'PDOException') $shortClassName = 'Database Error';

        // Get view
        ob_start();
        include $viewPath;
        $viewContent = ob_get_clean();

        // Get layout
        ob_start();
        include $layoutPath;
        $layoutContent = ob_get_clean();

        // Replace title and content
        $layoutContent = str_replace('{{title}}', $title, $layoutContent);
        $layoutContent = str_replace('{{content}}', $viewContent, $layoutContent);

        // Output
        echo $layoutContent;

        // Log
        self::log($exception);

        // Exit
        exit;
    }

    public static function frameworkSecondary(Throwable $exception): never
    {
        // Secondary exception handler to handle exceptions thrown in the primary exceptionHandler()
        // We cannot do layout/view here, so output is basic

        // Set status code
        http_response_code(500);

        // Output
        echo "<div style=\"font-size: 120%;\">\n";
        echo "<pre style=\"font-weight: bold;\">Error</pre>\n";
        echo "<pre>" . $exception->getMessage() . "</pre>\n";
        echo "<pre>" . $exception->getTraceAsString() . "</pre>\n";
        echo "</div>\n";

        // Log
        self::log($exception);

        // Exit
        exit;
    }

    public static function ajax(Throwable $exception): never
    {
        // Log
        self::log($exception);

        // Exit
        exit;
    }

    public static function client(string $message, Throwable $exception = null): never
    {
        // Get layout and view from config
        $layout = Functions::getConfig("page/error/client/layout");
        $view = Functions::getConfig("page/error/client/view");

        // Set default status code
        $statusCode = 500;

        // Check for exception
        if (is_object($exception)) {
            // Get the location from the object
            $traceInfoFile = $exception->getFile();
            $traceInfoLine = $exception->getLine();

            // Check for allowed status codes
            if (array_key_exists($exception->getCode(), self::HTTP_CODES)) {
                $statusCode = $exception->getCode();
            }
        } else {
            // Set location to null
            $traceInfoFile = null;
            $traceInfoLine = null;
        }

        // Set status code
        Application::app()->response()->setStatusCode($statusCode);

        // Set layout
        Application::app()->controller()->setLayout($layout);

        // Render view
        echo Application::app()->view()->renderView($view, ['exception' => $exception, 'code' => $statusCode, 'message' => $message, 'file' => $traceInfoFile, 'line' => $traceInfoLine]);

        // Update footer time
        Functions::time();

        // Log
        self::log($exception);

        // Exit
        exit;
    }

    public static function log(Throwable $exception): void
    {
        // Get exception details
        $exceptionDetails = new ExceptionDetails($exception);
        $shortClassName = $exceptionDetails->shortClassName;
        $exceptionMessage = $exceptionDetails->message;
        $exceptionPreviousMessage = $exceptionDetails->previousMessage;
        $exceptionCode = $exceptionDetails->code;
        $exceptionFile = $exceptionDetails->file;
        $exceptionLine = $exceptionDetails->line;
        $exceptionTraceString = $exceptionDetails->traceString;

        // Log
        $log = date('Y-m-d H:i:s') . "\n";
        $log .= "$shortClassName\n";
        if ($exceptionPreviousMessage) {
            $log .= "mess: $exceptionPreviousMessage\n";
        } else {
            $log .= "mess: $exceptionMessage\n";
        }
        $log .= "code: $exceptionCode\n";
        $log .= "file: $exceptionFile\n";
        $log .= "line: $exceptionLine\n";
        $log .= "$exceptionTraceString\n";
        $log .= "--------------------------------------------------------------------------------------------------------------------------------------------------------------\n";
        file_put_contents('/var/lib/photon/logs/exceptions.txt', $log, FILE_APPEND);
    }
}
