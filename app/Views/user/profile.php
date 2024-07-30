<?php

use App\Core\Application;

$title = 'Profile';

// Get values
$idValue = Application::app()->model('ProfileModel')->getProperty('id');
$firstnameValue = Application::app()->model('ProfileModel')->getProperty('firstname');
$lastnameValue = Application::app()->model('ProfileModel')->getProperty('lastname');
$emailValue = Application::app()->model('ProfileModel')->getProperty('email');
$newPasswordValue = Application::app()->model('ProfileModel')->getProperty('newPassword');
$confirmNewPasswordValue = Application::app()->model('ProfileModel')->getProperty('confirmNewPassword');
$newPasswordDisplay = Application::app()->model('ProfileModel')->getProperty('newPasswordDisplay');
$confirmNewPasswordDisplay = Application::app()->model('ProfileModel')->getProperty('confirmNewPasswordDisplay');

// Get errors
$firstnameError = Application::app()->model('ProfileModel')->getError('firstname');
$lastnameError = Application::app()->model('ProfileModel')->getError('lastname');
$emailError = Application::app()->model('ProfileModel')->getError('email');
$newPasswordError = Application::app()->model('ProfileModel')->getError('newPassword');
$confirmNewPasswordError = Application::app()->model('ProfileModel')->getError('confirmNewPassword');

// Get CSS class
$firstnameClass = Application::app()->model('ProfileModel')->getInputClass('firstname');
$lastnameClass = Application::app()->model('ProfileModel')->getInputClass('lastname');
$emailClass = Application::app()->model('ProfileModel')->getInputClass('email');
$newPasswordClass = Application::app()->model('ProfileModel')->getInputClass('newPassword');
$confirmNewPasswordClass = Application::app()->model('ProfileModel')->getInputClass('confirmNewPassword');

// Set CSS class on password visibility icons
$newPasswordShowClass = Application::app()->model('ProfileModel')->getPasswordIconClass('newPasswordDisplay', 'show');
$newPasswordHideClass = Application::app()->model('ProfileModel')->getPasswordIconClass('newPasswordDisplay', 'hide');
$confirmNewPasswordShowClass = Application::app()->model('ProfileModel')->getPasswordIconClass('confirmNewPasswordDisplay', 'show');
$confirmNewPasswordHideClass = Application::app()->model('ProfileModel')->getPasswordIconClass('confirmNewPasswordDisplay', 'hide');
?>
<div class="flex h-full">

    <form id="profile" action="/profile" method="POST" class="flex mx-auto">
        <div class="grid grid-cols-1 content-between mt-10 mb-10 w-[800px] overflow-y-auto p-8 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-650 rounded-xl">

            <div>
                <h2 class="text-2xl font-bold mb-10">Profile</h2>

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
                    <div class="w-[175px] mr-4 text-right content-center">New password</div>
                    <div class="w-[250px]"><input type="password" id="newPassword" name="newPassword" value="<?= $newPasswordValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $newPasswordClass ?> pr-[32px]"/></div>
                    <span id="newPasswordShow" class="<?= $newPasswordShowClass ?>password-display fa-solid fa-eye"></span>
                    <span id="newPasswordHide" class="<?= $newPasswordHideClass ?>password-display fa-regular fa-eye"></span>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $newPasswordError ?></div>
                </div>

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center">Confirm password</div>
                    <div class="w-[250px]"><input type="password" id="confirmNewPassword" name="confirmNewPassword" value="<?= $confirmNewPasswordValue ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $confirmNewPasswordClass ?> pr-[32px]"/></div>
                    <span id="confirmNewPasswordShow" class="<?= $confirmNewPasswordShowClass ?>password-display fa-solid fa-eye"></span>
                    <span id="confirmNewPasswordHide" class="<?= $confirmNewPasswordHideClass ?>password-display fa-regular fa-eye"></span>
                    <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $confirmNewPasswordError ?></div>
                </div>

                <div>
                    <input type="hidden" name="id" value="<?= $idValue ?>">
                </div>
            </div>

            <div>
                <div class="mb-5">Only enter the new password if you want to change your password.</div>
                <div class="flex">
                    <button type="submit" class="mr-5 primary">Update</button>
                    <button type="button" onClick="window.location.href='/profile';" class="secondary">Revert</button>
                </div>
            </div>

        </div>

        <input type="hidden" name="newPasswordDisplay" value="<?= $newPasswordDisplay ?>">
        <input type="hidden" name="confirmNewPasswordDisplay" value="<?= $confirmNewPasswordDisplay ?>">
    </form>

</div>

<script src="/js/profile.js"></script>
