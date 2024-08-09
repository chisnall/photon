<?php

declare(strict_types=1);

namespace App\Core;

final class Request
{
    private array $requestUri;

    public function __construct()
    {
        // Get request URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove illegal characters
        $requestUri = filter_var($requestUri, FILTER_SANITIZE_URL);

        // Parse the URI
        $requestArray = parse_url($requestUri);

        // Stop now if we cannot parse the URL
        // example: http://api//?var=123&var2=abc
        // Also stop if the path has been picked up in the parse URL
        // example: http://api//xxx
        if (!$requestArray || !array_key_exists('path', $requestArray)) {
            // Redirect to general error page
            header("Location: /error");
            exit;
        }

        // Update the query value to an array
        if (array_key_exists('query', $requestArray)) {
            // We can use parse_str() or just set it to the superglobal get array
            //parse_str($requestArray['query'],$requestArray['query']);
            $requestArray['query'] = $_GET;
        }

        // Check for final / character and remove it
        // Don't do this for the homepage
        if ( $requestArray['path'] != '/' && str_ends_with($requestArray['path'], '/')) {
            $requestArray['path'] = rtrim($requestArray['path'], '/');
        }

        // Update property
        $this->requestUri = $requestArray;
    }

    public function getPath(): string
    {
        return $this->requestUri['path'];
    }

    public function getQuery(): ?array
    {
        return $this->requestUri['query'] ?? null;
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'get';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'post';
    }

    public function getBody($filterSpecialChars = true): array
    {
        $body = [];

        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                if ($filterSpecialChars) {
                    $body[$key] = trim(filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS));
                } else {
                    $body[$key] = trim(filter_input(INPUT_GET, $key));
                }
            }
        }

        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                // Check for array first - used for checkbox arrays
                if (is_array($value)) {
                    $body[$key] = $value;
                }
                elseif ($filterSpecialChars) {
                    $body[$key] = trim(filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS));
                } else {
                    $body[$key] = trim(filter_input(INPUT_POST, $key));
                }
            }
        }

        return $body;
    }
}
