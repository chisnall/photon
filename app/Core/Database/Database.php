<?php

declare(strict_types=1);

namespace App\Core\Database;

abstract class Database
{
    protected Connection $connection;
    private static array $instances = [];

    public function __construct()
    {
        // Set driver name and version
        $this->connection->setProperty('driver', $this->connection->getAttribute(Connection::ATTR_DRIVER_NAME));
        $this->connection->setProperty('version', $this->connection->getAttribute(Connection::ATTR_SERVER_VERSION));
    }

    public static function getInstance(): self
    {
        $class = static::class;

        if (!array_key_exists($class, self::$instances)) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    public function getConnection(): ?Connection
    {
        return $this->connection;
    }
}
