<?php

declare(strict_types=1);

use App\Core\Database\Migrations;

$title = 'Database Migrations';

// Get pending migrations and update session
$pendingMigrations = (new Migrations())->getPendingMigrations();
session()->set('status/pendingMigrations', (bool)$pendingMigrations);
?>
<h1 class="pb-4 text-3xl font-bold">Database Migrations</h1>

<?php if ($pendingMigrations): ?>
    <div class="mb-2">There are outstanding database migrations:</div>
    <?php foreach ($pendingMigrations as $pendingMigration): ?>
        <div class="font-mono"><?= $pendingMigration ?></div>
    <?php endforeach; ?>
    <div class="mt-5">Run the migrations.php script as the root user to apply these migrations.</div>
<?php else: ?>
    <p>All migrations have been applied.</p>
<?php endif; ?>
