<?php

declare(strict_types=1);

use App\Core\Application;

$title = 'Registered';
?>
<h1 class="pb-4 text-3xl font-bold">Registration Complete</h1>
<p class="mb-5 text-lg">Thank you for registering.</p>

<?php
// Get the user email
// This would actually be implemented with an e-mail sent to the user in production system
$email = Application::app()->session()->get('user/registered');

if ($email) {
    // Get token from the user record
    $sql = "SELECT token FROM users WHERE email = '$email'";
    $token = Application::app()->db()->query($sql)->fetchColumn();

    // Create link
    $link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/register/activate?token=$token";

    // Show link
    echo "<p class=\"text-lg\">Click this link to activate your account:</p>\n";
    echo "<p class=\"text-lg\"><a class=\"hover:text-blue-600 dark:hover:text-red-700\" href='$link'>$link</a></p>\n";
}
