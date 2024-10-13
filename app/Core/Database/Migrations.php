<?php

declare(strict_types=1);

namespace App\Core\Database;

use Exception;

final class Migrations
{
    private string $driver;

    public function __construct()
    {
        // Set driver
        $this->driver = db()->driver();

        // Define migrations table and path
        define('MIGRATIONS_TABLE', '/app/Migrations/table/migrations_table.' . $this->driver . '.php');
        define('MIGRATIONS_PATH', '/app/Migrations/' . ucfirst($this->driver));
    }

    public function handleMigrations($argument): void
    {
        // For SQLite, check configuration is correct first
        if ($this->driver == 'sqlite') {
            // Get database file path
            $sqlitePath = getConfig("database/sqlite/path");

            // Get web server HTTP user
            $httpUserName = getConfig("app/httpUser");
            $httpUserInfo = posix_getpwnam($httpUserName);

            // Confirm HTTP user exists in the system
            if (!$httpUserInfo) {
                throw new Exception(message: "HTTP user account does not exist in the system: $httpUserName\nCheck your configuration for: app/httpUser");
            }

            // Get HTTP user group name
            $httpGroupId = $httpUserInfo['gid'];
            $httpGroupInfo = posix_getgrgid($httpGroupId);
            $httpGroupName = $httpGroupInfo['name'];
        }

        // Log
        $this->log(ucfirst($argument) . " migrations");
        $this->log("Driver: " . $this->driver);

        // Init arrays
        $newMigrations = [];
        $removedMigrations = [];

        // Create migrations table
        // For SQLite, if the database file does not exist, it will be created now as well
        $this->createMigrationsTable();

        // For SQLite, we need to set the file ownership, otherwise the web server user cannot write to the database
        if ($this->driver == 'sqlite') {
            // Confirm database file exists
            if (file_exists($sqlitePath)) {
                // Set ownership of directory
                $sqliteDir = dirname($sqlitePath);
                chown($sqliteDir, $httpUserName);
                chgrp($sqliteDir, $httpGroupName);

                // Set ownership of file
                chown($sqlitePath, $httpUserName);
                chgrp($sqlitePath, $httpGroupName);
            }
        }

        // Apply migrations
        if ($argument == "up") {
            // Get pending migrations
            $pendingMigrations = $this->getPendingMigrations();

            // Loop over pending migrations
            foreach ($pendingMigrations as $migration) {
                // Set migration name for log
                $migrationName = pathinfo($migration, PATHINFO_FILENAME);

                // Import class
                $instance = includeFile(file: MIGRATIONS_PATH . "/$migration.php");

                // Run migration
                $this->log("Applying migration $migrationName");
                $instance->up();
                $this->log("Applied migration $migrationName");
                $newMigrations[] = $migration;
            }

            // Record migrations
            if (count($newMigrations) > 0) {
                $this->saveMigrations($newMigrations);
            } else {
                $this->log("All migrations are applied");
            }
        }

        // Remove migrations
        elseif ($argument == "down") {
            // Get migrations already applied
            $appliedMigrations = $this->getAppliedMigrations();

            // Sort migrations in reverse order - we'll do the reverse sort on the keys
            krsort($appliedMigrations);

            // Loop over applied migrations
            foreach ($appliedMigrations as $migration) {
                // Set migration name for log
                $migrationName = pathinfo($migration, PATHINFO_FILENAME);

                // Import class
                $instance = includeFile(file: MIGRATIONS_PATH . "/$migration.php");

                // Run migration
                $this->log("Removing migration $migrationName");
                $instance->down();
                $this->log("Removed migration $migrationName");
                $removedMigrations[] = $migration;
            }

            // Record migrations
            if (count($removedMigrations) > 0) {
                $this->removeMigrations($removedMigrations);
            } else {
                $this->log("No migrations exist");
            }
        }
    }

    private function createMigrationsTable(): void
    {
        // Get SQL
        $sql = includeFile(file: MIGRATIONS_TABLE);

        // Create table
        db()->exec($sql);
    }

    private function getAvailableMigrations(): array
    {
        // Get files
        $files = array_diff(scandir(BASE_PATH . MIGRATIONS_PATH), ['.', '..']);

        // Process files and remove file extension
        foreach ($files as $fileKey => $fileName) {
            $files[$fileKey] = pathinfo($fileName, PATHINFO_FILENAME);
        }

        return $files;
    }

    public function getPendingMigrations(): array
    {
        // Get migrations already applied
        $appliedMigrations = $this->getAppliedMigrations();

        // Get migration files
        $files = $this->getAvailableMigrations();

        // Calc files we have not already applied
        $pendingMigrations = array_diff($files, $appliedMigrations);

        return $pendingMigrations;
    }

    private function getAppliedMigrations(): array
    {
        $sql = "SELECT migration FROM migrations";

        $statement = db()->prepare($sql);
        $statement->execute();
        $data = $statement->fetchAll(Connection::FETCH_COLUMN);

        return $data;
    }

    private function saveMigrations(array $migrations): void
    {
        // We want to convert our array
        // 0 => "2024_03_01_000000_initial.php"
        // 1 => "2024_04_01_000000_settings_table.php"
        // To this format:
        // ('2024_03_01_000000_initial', '2024-04-12 17:29:06'), ('2024_04_01_000000_settings_table', '2024-04-12 17:29:06')
        // That way we can insert the records with a single SQL query

        // array_map with traditional closure function
        //$migrationsInsert = array_map(function($m) { return "('$m')"; }, $migrations);

        // array_map with array function
        $insertedAt = date('Y-m-d H:i:s');
        $migrationsInsert = array_map(fn($m) => "('$m', '$insertedAt')", $migrations);

        // Create string from array
        $migrationsInsert = implode(', ', $migrationsInsert);

        // Insert records
        $sql = "INSERT INTO migrations (migration, created_at) VALUES $migrationsInsert";

        $statement = db()->prepare($sql);
        $statement->execute();
    }

    private function removeMigrations(array $migrations): void
    {
        // Loop the migrations
        foreach ($migrations as $migration) {
            $sql = "DELETE FROM migrations WHERE migration = '$migration'";

            $statement = db()->prepare($sql);
            $statement->execute();
        }
    }

    private function log($message): void
    {
        // We could extend this to log to a file
        echo "[" . date('Y-m-d H:i:s') . "] - " . $message . "\n";
    }

    public function databaseError($exception): never
    {
        // Get message
        $message = $exception->getMessage();

        // Output and exit
        echo "\nError\n\n";
        echo "$message\n\n";
        exit;
    }
}
