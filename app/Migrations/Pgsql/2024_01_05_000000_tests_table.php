<?php

declare(strict_types=1);

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS tests (
                id SERIAL PRIMARY KEY,
                request_id INTEGER NOT NULL,
                test_name VARCHAR(255) NOT NULL,
                test_type VARCHAR(255) NOT NULL,
                test_assertion VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT tests_request_id_fkey
                    FOREIGN KEY (request_id) REFERENCES requests (id)
                    ON DELETE CASCADE
                );";

        db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE tests;";

        db()->exec($sql);
    }
};
