<?php

declare(strict_types=1);

$title = 'Profile';

// Get values
$idValue = model('ProfileModel')->getProperty('id');
$firstnameValue = model('ProfileModel')->getProperty('firstname');
$lastnameValue = model('ProfileModel')->getProperty('lastname');
$emailValue = model('ProfileModel')->getProperty('email');
$newPasswordValue = model('ProfileModel')->getProperty('newPassword');
$confirmNewPasswordValue = model('ProfileModel')->getProperty('confirmNewPassword');
$newPasswordDisplay = model('ProfileModel')->getProperty('newPasswordDisplay');
$confirmNewPasswordDisplay = model('ProfileModel')->getProperty('confirmNewPasswordDisplay');

// Get errors
$firstnameError = model('ProfileModel')->getError('firstname');
$lastnameError = model('ProfileModel')->getError('lastname');
$emailError = model('ProfileModel')->getError('email');
$newPasswordError = model('ProfileModel')->getError('newPassword');
$confirmNewPasswordError = model('ProfileModel')->getError('confirmNewPassword');

// Get CSS class
$firstnameClass = model('ProfileModel')->getInputClass('firstname');
$lastnameClass = model('ProfileModel')->getInputClass('lastname');
$emailClass = model('ProfileModel')->getInputClass('email');
$newPasswordClass = model('ProfileModel')->getInputClass('newPassword');
$confirmNewPasswordClass = model('ProfileModel')->getInputClass('confirmNewPassword');

// Set CSS class on password visibility icons
$newPasswordShowClass = model('ProfileModel')->getPasswordIconClass('newPasswordDisplay', 'show');
$newPasswordHideClass = model('ProfileModel')->getPasswordIconClass('newPasswordDisplay', 'hide');
$confirmNewPasswordShowClass = model('ProfileModel')->getPasswordIconClass('confirmNewPasswordDisplay', 'show');
$confirmNewPasswordHideClass = model('ProfileModel')->getPasswordIconClass('confirmNewPasswordDisplay', 'hide');
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
