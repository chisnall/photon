<?php

declare(strict_types=1);

use App\Core\Application;

return new class
{
    public function up(): void
    {
        $sql = "ALTER TABLE requests
                ADD COLUMN sort_order INT
                ;";

        Application::app()->db()->exec($sql);
    }

    public function down(): void
    {
        $sql = "ALTER TABLE requests
                DROP COLUMN sort_order
                ;";

        Application::app()->db()->exec($sql);
    }
};
