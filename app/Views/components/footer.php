<?php

declare(strict_types=1);

use App\Core\Application;
use App\Functions\Output;
?>
<div class="fixed bottom-0 z-50 w-full h-[41px] font-semibold
                bg-gradient-to-b from-zinc-100 dark:from-zinc-800
                bg-white dark:bg-black border-t border-zinc-300 dark:border-zinc-650 text-zinc-700 dark:text-zinc-300">
    <div class="flex justify-between content-center h-full">
        <div class="flex flex-row">
            <div class="footer-item flex justify-center border-r min-w-[100px]">
                <div id="app-time" class="content-center"></div>
            </div>
            <?php if (APP_DEBUG): ?>
            <div class="footer-item flex justify-center border-r min-w-[100px]">
                <div class="content-center"><?= Application::app()->controller()->getProperty('page')['layout'] ?></div>
            </div>
            <div class="footer-item flex justify-center border-r min-w-[100px]">
                <div class="content-center"><?= Application::app()->controller()->getProperty('page')['view'] ?></div>
            </div>
            <?php endif; ?>
            <?php if (Application::app()->session()->get('status/pendingMigrations')): ?>
            <div class="footer-item flex justify-center border-r min-w-[100px] text-white bg-red-600 dark:bg-red-700">
                <div class="content-center"><a href="/migrations">MIGRATIONS</a></div>
            </div>
            <?php endif; ?>
            <?php if (Application::app()->getProperty('errors')): ?>
            <div class="footer-item flex justify-center border-r min-w-[100px] text-white bg-red-600 dark:bg-red-700">
                <div class="content-center">ERRORS: <?= Application::app()->getProperty('errors') ?></div>
            </div>
            <?php endif; ?>
        </div>
        <div class="flex flex-row">
            <div class="footer-item border-l">
                <a href="<?= APP_DOCKER ?>" target="_blank"><?= Output::icon('docker') ?></a>
            </div>
            <div class="footer-item border-l">
                <a href="<?= APP_GITHUB ?>" target="_blank"><?= Output::icon('github') ?></a>
            </div>
            <div class="footer-item flex items-center text-nowrap border-l">
                <?= Output::dbInfo(); ?>
            </div>
            <div class="footer-item text-nowrap border-l">
                Photon <?= APP_VERSION ?>
            </div>
        </div>
    </div>
</div>
