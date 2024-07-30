<?php
/** @var array $params */

$title = 'Error';
$exception = $params["exception"];
$code = $params["code"];
$message = $params["message"];
$file = $params["file"];
$line = $params["line"];

// Default show trace
$trace = false;

// Check for exception object
if (is_object($exception)) {
    // Get class name and set heading
    $className = get_class($exception);

    // Check for database error
    if ($className == 'PDOException') {
        $className = 'Database Error';
    }

    // Check for client errors
    if ($code >= 400 && $code <= 499) {
        // Set heading
        $heading = "Error / $code";
    } else {
        // Set heading and trace
        $heading = $className;
        $trace = true;
    }
} else {
    // Set heading
    $heading = 'Error';
}

// Show heading
echo "<h1 class=\"pb-4 text-3xl font-bold\">$heading</h1>\n";

// Show trace
if ($trace) {
    # Show location
    echo "<p class=\"font-mono\">File: $file</p>\n";
    echo "<p class=\"mb-2 font-mono\">Line: $line</p>\n";

    // Show stack trace
    if (method_exists($exception, 'getMessage') && method_exists($exception, 'getTraceAsString')) {
        echo "<pre class=\"whitespace-pre-wrap mb-2 text-red-600 dark:text-red-600\">" . $exception->getMessage() . "</pre>\n";
        echo "<pre class=\"whitespace-pre-wrap\">" . $exception->getTraceAsString() . "</pre>\n";
    }
} else {
    // Show message
    echo "<p class=\"mb-5\">$message</p>\n";
}
