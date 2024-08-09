<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
    private const string FLASH_KEY = 'flash_messages';

    public function __construct(protected string $from)
    {
        // Start session
        // This may be run from the ajax.php script, so check for active session first
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Check if running from application
        if ( $from == "application" ) {
            // Mark flash messages for removal
            $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
            foreach ($flashMessages as &$flashMessage) {
                $flashMessage['remove'] = true;
            }

            // Update session array
            $_SESSION[self::FLASH_KEY] = $flashMessages;
        }
    }

    public function __destruct()
    {
        // Remove flash messages
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }

        // Update session array
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function countFlash(): int
    {
        return count($_SESSION[self::FLASH_KEY]);
    }

    public function setFlash($type, $message, $remove = false): void
    {
        // Set "remove" to true if setting flash messages in the framework before page load
        // otherwise the flash message will be shown again on page reload

        // Get flash message class
        $class = Functions::getConfig("page/flash/$type");

        // Register session
        $_SESSION[self::FLASH_KEY][] = [
            'remove' => $remove,
            'type' => $type,
            'message' => $message,
            'class' => $class,
        ];
    }

    public function getFlash(): ?array
    {
        // return the first flash message - we can only display one
        return $_SESSION[self::FLASH_KEY][0] ?? null;
    }

    public function set($type, $value): void
    {
        // Convert type to array
        $typeArray = explode('/', $type);

        // Check key depth
        if (array_key_exists(2, $typeArray)) {
            $_SESSION[$typeArray[0]][$typeArray[1]][$typeArray[2]] = $value;
        } elseif (array_key_exists(1, $typeArray)) {
            $_SESSION[$typeArray[0]][$typeArray[1]] = $value;
        } else {
            $_SESSION[$typeArray[0]] = $value;
        }
    }

    public function get($type): mixed
    {
        // Convert type to array
        $typeArray = explode('/', $type);

        // Check key depth
        if (array_key_exists(2, $typeArray)) {
            return $_SESSION[$typeArray[0]][$typeArray[1]][$typeArray[2]] ?? null;
        } elseif (array_key_exists(1, $typeArray)) {
            return $_SESSION[$typeArray[0]][$typeArray[1]] ?? null;
        } else {
            return $_SESSION[$typeArray[0]] ?? null;
        }
    }

    public function remove($type): void
    {
        // Convert type to array
        $typeArray = explode('/', $type);

        // Check key depth
        if (array_key_exists(2, $typeArray)) {
            unset($_SESSION[$typeArray[0]][$typeArray[1]][$typeArray[2]]);
        } elseif (array_key_exists(1, $typeArray)) {
            unset($_SESSION[$typeArray[0]][$typeArray[1]]);
        } else {
            unset($_SESSION[$typeArray[0]]);
        }
    }

    public function setReferer(): void
    {
        // Check for referer key in the server superglobal - it won't exist if the user has visited the page directly
        if (array_key_exists('HTTP_REFERER', $_SERVER)) {
            // Get path from referer
            $refererPath = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);

            // Ignore certain views like login and register
            if ( !in_array($refererPath, Functions::getConfig("no-referer"))) {
                $this->set('page/referer', $refererPath);
            }
        }
    }

    public function getReferer()
    {
        // Default to homepage if not set, since we use this on redirects
        return $this->get('page/referer') ?? '/';
    }
}
