<?php

declare(strict_types=1);

return new class
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS collections (
                id SERIAL PRIMARY KEY,
                user_id INTEGER NOT NULL,
                collection_name VARCHAR(255) NOT NULL,
                collection_variables TEXT,
                created_at TIMESTAMP NOT NULL,
                updated_at TIMESTAMP NOT NULL,

                CONSTRAINT collections_user_id_fkey
                    FOREIGN KEY (user_id) REFERENCES users (id)
                    ON DELETE CASCADE
                );";

        db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE collections;";

        db()->exec($sql);
    }
};
