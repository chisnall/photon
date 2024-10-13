<?php

declare(strict_types=1);

$responseBodyContent = session()->get('response/responseBodyContent');
?>
<!doctype html>
<html id="html" lang="en">

<head>
    <script src="/js/theme-header.js"></script>
    <link href="/css/iframe.css" rel="stylesheet">
</head>

<?= $responseBodyContent ?>
