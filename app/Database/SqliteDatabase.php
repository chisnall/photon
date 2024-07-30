<?php

namespace App\Database;

use App\Core\Database\Connection;
use App\Core\Database\Database;
use App\Core\Functions;
use PDOException;

class SqliteDatabase extends Database
{
    protected function __construct()
    {
        // Get settings
        $dbDriver = "sqlite";
        $dbPath = Functions::getConfig("database/$dbDriver/path");

        // Confirm database exists
        if (!file_exists($dbPath)) {
            // Get trace file (start)
            $traceInfoFile = Functions::traceInfo("start")['file'];

            // Halt if not running from migrations.php script
            if ($traceInfoFile != 'migrations.php') {
                throw new PDOException("Database does not exist. You need to run migrations first.");
            }
        }

        // Create DSN (Data Source Name) string
        $dsn = "sqlite:$dbPath";

        // Additional options
        $dbOptions = [
            Connection::ATTR_ERRMODE            => Connection::ERRMODE_EXCEPTION,
            Connection::ATTR_DEFAULT_FETCH_MODE => Connection::FETCH_ASSOC,
        ];

        // Confirm directory exists
        $dbDir = dirname($dbPath);
        if (!file_exists($dbDir)) {
            mkdir($dbDir);
        }

        // Attempt connection
        $this->connection = new Connection(dsn: $dsn, options: $dbOptions);

        // Enable foreign key support
        $this->connection->exec('PRAGMA foreign_keys = ON;');

        // Run constructor in parent
        parent::__construct();
    }
}
