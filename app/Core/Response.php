<?php

namespace App\Core;

final class Response
{
    public function setStatusCode(int $code): void
    {
        // Can only set this if headers not already sent
        if (!headers_sent()) {
            http_response_code($code);
        }
    }

    public function redirect(string $path): never
    {
        header("Location: $path");
        exit;
    }
}
