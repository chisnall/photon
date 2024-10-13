<?php

declare(strict_types=1);

namespace App\Database;

use App\Core\Database\Connection;
use App\Core\Database\Database;

class PostgresDatabase extends Database
{
    protected function __construct()
    {
        // Get connection settings
        $dbDriver = "pgsql";
        $dbHost = getConfig("database/$dbDriver/host");
        $dbPort = getConfig("database/$dbDriver/port", true);
        $dbSchema = getConfig("database/$dbDriver/schema");
        $dbUsername = getConfig("database/$dbDriver/username");
        $dbPassword = getConfig("database/$dbDriver/password");

        // Create DSN (Data Source Name) string
        // We allow a null port, since PDO will use a default port, so check port first
        if ($dbPort) $dbPort = ";port=$dbPort";
        $dsn = "$dbDriver:host=$dbHost$dbPort;dbname=$dbSchema";

        // Additional options
        $dbOptions = [
            Connection::ATTR_ERRMODE            => Connection::ERRMODE_EXCEPTION,
            Connection::ATTR_DEFAULT_FETCH_MODE => Connection::FETCH_ASSOC,
        ];

        // Attempt connection
        $this->connection = new Connection(dsn: $dsn, username: $dbUsername, password: $dbPassword, options: $dbOptions);

        // Run constructor in parent
        parent::__construct();
    }
}
