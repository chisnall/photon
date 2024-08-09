<?php

declare(strict_types=1);

namespace App\Database;

use App\Core\Database\Connection;
use App\Core\Database\Database;
use App\Core\Functions;

class PostgresDatabase extends Database
{
    protected function __construct()
    {
        // Get connection settings
        $dbDriver = "pgsql";
        $dbHost = Functions::getConfig("database/$dbDriver/host");
        $dbPort = Functions::getConfig("database/$dbDriver/port", true);
        $dbSchema = Functions::getConfig("database/$dbDriver/schema");
        $dbUsername = Functions::getConfig("database/$dbDriver/username");
        $dbPassword = Functions::getConfig("database/$dbDriver/password");

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
