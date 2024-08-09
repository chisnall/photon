<?php

declare(strict_types=1);

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                status TINYINT DEFAULT 0 NOT NULL,
                firstname VARCHAR(255) NOT NULL,
                lastname VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL
                );";

        Application::app()->db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE users;";

        Application::app()->db()->exec($sql);
    }
};
