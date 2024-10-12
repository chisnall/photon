<?php
session_save_path('/var/lib/photon/sessions');
session_start();
$responseBodyContent = $_SESSION['response']['responseBodyContent'] ?? null;
?>

<!doctype html>
<html id="html" lang="en">

<head>
    <script src="/js/theme-header.js"></script>
    <link href="/css/iframe.css" rel="stylesheet">
</head>

<?= $responseBodyContent ?>
