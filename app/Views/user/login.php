<?php

declare(strict_types=1);

use App\Functions\Output;

$title = 'Login';

// Get values
$emailValue = model('LoginModel')->getProperty('email');
$passwordValue = model('LoginModel')->getProperty('password');
$passwordDisplay = model('LoginModel')->getProperty('passwordDisplay');
$formAction = model('LoginModel')->getProperty('formAction');

// Get errors
$emailError = model('LoginModel')->getError('email');
$passwordError = model('LoginModel')->getError('password');

// Get CSS class
$emailClass = model('LoginModel')->getInputClass('email');
$passwordClass = model('LoginModel')->getInputClass('password');

// Make login error ambiguous - i.e. don't give an attacker a clue to which input is incorrect
if ($formAction == 'login' && ($emailError == 'Account does not exist' || $passwordError == 'Password is incorrect')) {
    // Set email error and remove password error if password is provided
    $emailError = 'Incorrect email or password';
    if ($passwordValue) $passwordError = null;

    // Set email and password classes
    $emailClass = 'input-error';
    $passwordClass = 'input-error';
} elseif ($formAction == 'login' && $passwordValue === null && ($emailError === null || $emailError == 'Account is not activated')) {
    // Don't allow the attacker to know the difference between an email that is registered and one that is not
    // This is to cover where the password is not entered and the the attacker keeps trying different emails
    // Let's make the form error consistent whether the email is registered or not
    // We will also not allow the attacker to know if the email is registered where the account is not activated yet

    // Set email error
    $emailError = 'Incorrect email or password';

    // Set email class
    $emailClass = 'input-error';
}

// Set CSS class on password visibility icons
$passwordShowClass = model('LoginModel')->getPasswordIconClass('passwordDisplay', 'show');
$passwordHideClass = model('LoginModel')->getPasswordIconClass('passwordDisplay', 'hide');
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
            <input type="hidden" name="formAction" value="login">
        </form>
    </div>
</div>

<script src="/js/login.js"></script>
