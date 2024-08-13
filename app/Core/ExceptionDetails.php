<?php

declare(strict_types=1);

namespace App\Core;

use ReflectionClass;
use Throwable;

class ExceptionDetails
{
    public ?string $className;
    public ?string $shortClassName;
    public ?string $message;
    public ?string $previousMessage;
    public ?string $code;
    public ?string $file;
    public ?int $line;
    public ?array $trace;
    public ?string $traceString;

    public function __construct(Throwable $exception)
    {
        // Get class name and exception message
        $className = get_class($exception);
        $message = $exception->getMessage();

        // Get short class name
        $shortClassName = (new ReflectionClass($className))->getShortName();

        // Check for previous exception
        if ($exception->getPrevious()) {
            // Get exception details
            $previousMessage = $exception->getPrevious()->getMessage();
            $code = $exception->getPrevious()->getCode();
            $file = $exception->getPrevious()->getFile();
            $line = $exception->getPrevious()->getLine();
            $trace = $exception->getPrevious()->getTrace();
            $traceString = $exception->getPrevious()->getTraceAsString();
        } else {
            // Get exception details
            $previousMessage = null;
            $code = $exception->getCode();
            $file = $exception->getFile();
            $line = $exception->getLine();
            $trace = $exception->getTrace();
            $traceString = $exception->getTraceAsString();
        }

        // Set properties
        $this->className = $className;
        $this->shortClassName = $shortClassName;
        $this->message = $message;
        $this->previousMessage = $previousMessage;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->trace = $trace;
        $this->traceString = $traceString;
    }
}
