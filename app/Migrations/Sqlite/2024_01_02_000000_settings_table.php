<?php

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY,
                user_id INTEGER NOT NULL,
                user_settings TEXT NOT NULL,
                global_variables TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT `settings_user_id_fkey`
                    FOREIGN KEY (user_id) REFERENCES users (id)
                    ON DELETE CASCADE
                );";

        Application::app()->db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE settings;";

        Application::app()->db()->exec($sql);
    }
};
