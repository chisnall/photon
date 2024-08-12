<?php

declare(strict_types=1);

use App\Core\Application;
use App\Functions\Output;
use App\Models\UserModel;

?>
<nav class="fixed top-0 z-50 w-full h-[41px] font-semibold
                bg-gradient-to-t from-zinc-100 dark:from-zinc-800
                bg-white dark:bg-black border-b border-zinc-300 dark:border-zinc-650 text-zinc-700 dark:text-zinc-300">
    <div class="flex justify-between content-center h-full">
        <div class="flex flex-row">
            <?php if (UserModel::isLoggedIn()): ?>
            <a href="/">
                <div class="menu-item menu-item-hover border-r">Home</div>
            </a>
            <a href="/tests">
                <div class="menu-item menu-item-hover border-r">Tests</div>
            </a>
            <a href="/help">
                <div class="menu-item menu-item-hover border-r">Help</div>
            </a>
            <a href="/about">
                <div class="menu-item menu-item-hover border-r">About</div>
            </a>
            <?php else: ?>
            <div class="menu-item border-r text-zinc-400 dark:text-zinc-500">Home</div>
            <div class="menu-item border-r text-zinc-400 dark:text-zinc-500">Tests</div>
            <div class="menu-item border-r text-zinc-400 dark:text-zinc-500">Help</div>
            <div class="menu-item border-r text-zinc-400 dark:text-zinc-500">About</div>
            <?php endif; ?>
        </div>
        <div class="flex flex-row">
            <div class="menu-item">Photon</div>
        </div>
        <div class="flex flex-row">
            <?php if (UserModel::isLoggedIn()): ?>
            <div class="menu-item flex items-center">
                <div><?= UserModel::getDisplayName() ?></div>
                <div class="ml-1">
                    <?= Output::icon('welcome') ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="menu-item flex px-3 border-l">
                <?php if (UserModel::isLoggedIn()): ?>
                <div class="content-center">
                    <a href="/profile">
                        <button type="button" class="block px-2 py-0 m-0 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700">
                            <?= Output::icon('user-profile') ?>
                        </button>
                    </a>
                </div>
                <div class="content-center">
                    <a href="/settings">
                        <button type="button" class="block px-2 py-0 m-0 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700">
                            <?= Output::icon('settings') ?>
                        </button>
                    </a>
                </div>
                <?php else: ?>
                <div class="content-center">
                    <button type="button" class="block text-zinc-400 dark:text-zinc-500 px-2 rounded-lg" disabled>
                        <?= Output::icon('user-profile') ?>
                    </button>
                </div>
                <div class="content-center">
                    <button type="button" class="block text-zinc-400 dark:text-zinc-500 px-2 rounded-lg" disabled>
                        <?= Output::icon('settings') ?>
                    </button>
                </div>
                <?php endif; ?>
                <div class="content-center">
                    <button type="button" id="theme-toggle" class="block px-2 py-0 m-0 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700">
                        <?= Output::icon('theme-toggle') ?>
                    </button>
                </div>
            </div>
            <?php if (UserModel::isLoggedIn()): ?>
            <a href="/logout">
                <div class="menu-item menu-item-hover border-l">Logout</div>
            </a>
            <?php else: ?>
            <a href="/login">
                <div class="menu-item menu-item-hover border-l">Login</div>
            </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php
if (Application::app()->controller()->getProperty('page')['view'] != 'user/login') {
    echo "<input type='hidden' id='ajaxToken' name='ajaxToken' value='" . Application::app()->session()->get('user/token') . "'>\n";
}
?>
