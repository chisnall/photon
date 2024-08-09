<?php

declare(strict_types=1);

use App\Core\Application;

$title = 'Register';

// Get values
$firstnameValue = Application::app()->model('RegisterModel')->getProperty('firstname');
$lastnameValue = Application::app()->model('RegisterModel')->getProperty('lastname');
$emailValue = Application::app()->model('RegisterModel')->getProperty('email');
$passwordValue = Application::app()->model('RegisterModel')->getProperty('password');
$confirmPasswordValue = Application::app()->model('RegisterModel')->getProperty('confirmPassword');
$passwordDisplay = Application::app()->model('RegisterModel')->getProperty('passwordDisplay');
$confirmPasswordDisplay = Application::app()->model('RegisterModel')->getProperty('confirmPasswordDisplay');

// Get errors
$firstnameError = Application::app()->model('RegisterModel')->getError('firstname');
$lastnameError = Application::app()->model('RegisterModel')->getError('lastname');
$emailError = Application::app()->model('RegisterModel')->getError('email');
$passwordError = Application::app()->model('RegisterModel')->getError('password');
$confirmPasswordError = Application::app()->model('RegisterModel')->getError('confirmPassword');

// Get CSS class
$firstnameClass = Application::app()->model('RegisterModel')->getInputClass('firstname');
$lastnameClass = Application::app()->model('RegisterModel')->getInputClass('lastname');
$emailClass = Application::app()->model('RegisterModel')->getInputClass('email');
$passwordClass = Application::app()->model('RegisterModel')->getInputClass('password');
$confirmPasswordClass = Application::app()->model('RegisterModel')->getInputClass('confirmPassword');

// Set CSS class on password visibility icons
$passwordShowClass = Application::app()->model('RegisterModel')->getPasswordIconClass('passwordDisplay', 'show');
$passwordHideClass = Application::app()->model('RegisterModel')->getPasswordIconClass('passwordDisplay', 'hide');
$confirmPasswordShowClass = Application::app()->model('RegisterModel')->getPasswordIconClass('confirmPasswordDisplay', 'show');
$confirmPasswordHideClass = Application::app()->model('RegisterModel')->getPasswordIconClass('confirmPasswordDisplay', 'hide');
?>
<div class="flex h-full">

    <form id="register" action="/register" method="POST" class="flex mx-auto">
        <div class="grid grid-cols-1 content-between mt-10 mb-10 w-[800px] overflow-y-auto p-8 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-650 rounded-xl">
            <div>
                <h2 class="text-2xl font-bold mb-10">Register</h2>

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center">First name</div>
                    <div class="w-[250px]"><input type="text" name="firstname" value="<?= $firstnameValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $firstnameClass ?>"/></div>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $firstnameError ?></div>
                </div>

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center">Last name</div>
                    <div class="w-[250px]"><input type="text" name="lastname" value="<?= $lastnameValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $lastnameClass ?>"/></div>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $lastnameError ?></div>
                </div>

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center">Email</div>
                    <div class="w-[250px]"><input type="text" name="email" value="<?= $emailValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $emailClass ?>"/></div>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $emailError ?></div>
                </div>

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center">Password</div>
                    <div class="w-[250px]"><input type="password" id="password" name="password" value="<?= $passwordValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $passwordClass ?> pr-[32px]"/></div>
                    <span id="passwordShow" class="<?= $passwordShowClass ?>password-display fa-solid fa-eye"></span>
                    <span id="passwordHide" class="<?= $passwordHideClass ?>password-display fa-regular fa-eye"></span>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $passwordError ?></div>
                </div>

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center">Confirm password</div>
                    <div class="w-[250px]"><input type="password" id="confirmPassword" name="confirmPassword" value="<?= $confirmPasswordValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $confirmPasswordClass ?> pr-[32px]"/></div>
                    <span id="confirmPasswordShow" class="<?= $confirmPasswordShowClass ?>password-display fa-solid fa-eye"></span>
                    <span id="confirmPasswordHide" class="<?= $confirmPasswordHideClass ?>password-display fa-regular fa-eye"></span>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $confirmPasswordError ?></div>
                </div>
            </div>

            <div>
                <div>
                    <button type="submit" class="primary">Register</button>
                </div>
            </div>

        </div>

        <input type="hidden" name="passwordDisplay" value="<?= $passwordDisplay ?>">
        <input type="hidden" name="confirmPasswordDisplay" value="<?= $confirmPasswordDisplay ?>">
    </form>

</div>

<script src="/js/register.js"></script>
