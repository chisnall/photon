<?php

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS settings (
                id SERIAL PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                user_settings MEDIUMTEXT NOT NULL,
                global_variables MEDIUMTEXT NOT NULL,
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
