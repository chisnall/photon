<?php

declare(strict_types=1);

use App\Core\Application;
use App\Functions\Output;

$title = 'Login';

// Get values
$emailValue = Application::app()->model('LoginModel')->getProperty('email');
$passwordValue = Application::app()->model('LoginModel')->getProperty('password');
$passwordDisplay = Application::app()->model('LoginModel')->getProperty('passwordDisplay');

// Get errors
$emailError = Application::app()->model('LoginModel')->getError('email');
$passwordError = Application::app()->model('LoginModel')->getError('password');

// Get CSS class
$emailClass = Application::app()->model('LoginModel')->getInputClass('email');
$passwordClass = Application::app()->model('LoginModel')->getInputClass('password');

// Set CSS class on password visibility icons
$passwordShowClass = Application::app()->model('LoginModel')->getPasswordIconClass('passwordDisplay', 'show');
$passwordHideClass = Application::app()->model('LoginModel')->getPasswordIconClass('passwordDisplay', 'hide');
?>
<div class="flex h-full pb-20">
    <div class="w-[500px] m-auto bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-650 p-8 rounded-xl">
        <h2 class="text-2xl font-bold mb-8">Login</h2>
        <form id="login" action="/login" method="POST">
            <div class="mb-6">
                <div class="mb-2">Email</div>
                <div><input type="text" name="email" value="<?= $emailValue ?>" spellcheck="false" class="<?= $emailClass ?>"/></div>
                <div class="form-error"><?= $emailError ?></div>
            </div>
            <div class="mb-6">
                <div class="mb-2">Password</div>
                <div class="flex">
                    <div class="w-full"><input type="password" id="password" name="password" value="<?= $passwordValue ?>" spellcheck="false" class="<?= $passwordClass ?> pr-[32px]"/></div>
                    <span id="passwordShow" class="<?= $passwordShowClass ?>password-display fa-solid fa-eye"></span>
                    <span id="passwordHide" class="<?= $passwordHideClass ?>password-display fa-regular fa-eye"></span>
                </div>
                <div class="form-error"><?= $passwordError ?></div>
            </div>
            <div>
                <button type="submit" class="primary">Login</button>
            </div>
            <div class="mt-8">
                <a href="/register" class="block hover:text-blue-600 dark:hover:text-red-700">
                    <div class="flex">
                        <div>
                            <?= Output::icon('user-add') ?>
                        </div>
                        <div class="ml-1">Click here to register</div>
                    </div>
                </a>
            </div>

            <input type="hidden" name="passwordDisplay" value="<?= $passwordDisplay ?>">
        </form>
    </div>
</div>

<script src="/js/login.js"></script>
