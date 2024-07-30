<?php

namespace App\Core\Database;

use App\Core\Traits\GetSetProperty;
use PDO;

class Connection extends PDO
{
    use GetSetProperty;

    private readonly string $driver;
    private readonly string $version;

    public function driver(): string
    {
        return $this->driver;
    }

    public function version(): string
    {
        return $this->version;
    }
}
