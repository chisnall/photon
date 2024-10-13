<?php

declare(strict_types=1);

$title = 'Register';

// Get values
$firstnameValue = model('RegisterModel')->getProperty('firstname');
$lastnameValue = model('RegisterModel')->getProperty('lastname');
$emailValue = model('RegisterModel')->getProperty('email');
$passwordValue = model('RegisterModel')->getProperty('password');
$confirmPasswordValue = model('RegisterModel')->getProperty('confirmPassword');
$passwordDisplay = model('RegisterModel')->getProperty('passwordDisplay');
$confirmPasswordDisplay = model('RegisterModel')->getProperty('confirmPasswordDisplay');
$createCollection = model('RegisterModel')->getProperty('createCollection') ?? 'on';

// Set checkbox checked value
$createCollection == 'on' ? $createCollectionChecked = ' checked' : $createCollectionChecked = null;

// Get errors
$firstnameError = model('RegisterModel')->getError('firstname');
$lastnameError = model('RegisterModel')->getError('lastname');
$emailError = model('RegisterModel')->getError('email');
$passwordError = model('RegisterModel')->getError('password');
$confirmPasswordError = model('RegisterModel')->getError('confirmPassword');

// Get CSS class
$firstnameClass = model('RegisterModel')->getInputClass('firstname');
$lastnameClass = model('RegisterModel')->getInputClass('lastname');
$emailClass = model('RegisterModel')->getInputClass('email');
$passwordClass = model('RegisterModel')->getInputClass('password');
$confirmPasswordClass = model('RegisterModel')->getInputClass('confirmPassword');

// Set CSS class on password visibility icons
$passwordShowClass = model('RegisterModel')->getPasswordIconClass('passwordDisplay', 'show');
$passwordHideClass = model('RegisterModel')->getPasswordIconClass('passwordDisplay', 'hide');
$confirmPasswordShowClass = model('RegisterModel')->getPasswordIconClass('confirmPasswordDisplay', 'show');
$confirmPasswordHideClass = model('RegisterModel')->getPasswordIconClass('confirmPasswordDisplay', 'hide');
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

                <div class="flex flex-row mb-6">
                    <div class="w-[175px] mr-4 text-right content-center"></div>
                    <div class="w-[25px]">
                        <input type="checkbox" name="createCollectionCheckbox" id="createCollectionCheckbox"<?= $createCollectionChecked ?>>
                    </div>
                    <div class="mr-4 content-center">
                        <label for="createCollectionCheckbox" class="select-none">Create example collection</label>
                    </div>
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
        <input type="hidden" name="createCollection" id="createCollection" value="<?= $createCollection ?>">
    </form>

</div>

<script src="/js/register.js"></script>
