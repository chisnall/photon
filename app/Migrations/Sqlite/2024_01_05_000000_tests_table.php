<?php

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS tests (
                id INTEGER PRIMARY KEY,
                request_id INTEGER NOT NULL,
                test_name TEXT NOT NULL,
                test_type TEXT NOT NULL,
                test_assertion TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT `tests_request_id_fkey`
                    FOREIGN KEY (request_id) REFERENCES requests (id)
                    ON DELETE CASCADE
                );";

        Application::app()->db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE tests;";

        Application::app()->db()->exec($sql);
    }
};
