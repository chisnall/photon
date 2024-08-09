<?php

declare(strict_types=1);

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS groups (
                id SERIAL PRIMARY KEY,
                user_id INTEGER NOT NULL,
                group_name VARCHAR(255) NOT NULL,
                group_requests TEXT,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT groups_user_id_fkey
                    FOREIGN KEY (user_id) REFERENCES users (id)
                    ON DELETE CASCADE
                );";

        Application::app()->db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE groups;";

        Application::app()->db()->exec($sql);
    }
};
