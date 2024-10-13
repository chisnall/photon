<?php

declare(strict_types=1);

return new class
{
    public function up(): void
    {
        $sql = "ALTER TABLE requests
                ADD COLUMN sort_order INT
                ;";

        db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "ALTER TABLE requests
                DROP COLUMN sort_order
                ;";

        db()->exec($sql);
    }
};
