<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Functions;
?>
<!doctype html>
<html id="html" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/images/docker.png" sizes="192x192">
    <link href="/css/fontawesome.min.css" rel="stylesheet">
    <link href="/css/regular.min.css" rel="stylesheet">
    <link href="/css/solid.min.css" rel="stylesheet">
    <link href="/css/jquery-ui-1.13.3.min.css" rel="stylesheet">
    <link href="/css/photon.css" rel="stylesheet">
    <link href="/css/tailwind.css" rel="stylesheet">
    <script src="/js/jquery-3.7.1.min.js"></script>
    <script src="/js/jquery-ui-1.13.3.min.js"></script>
    <script src="/js/theme-header.js"></script>
    <title>{{title}}</title>
</head>
<body class="body-text">

<?php Functions::includeFile(file: '/app/Views/components/header.php'); ?>
<?php Functions::includeFile(file: '/app/Views/components/footer.php'); ?>
<?php if (Application::app()->session()->countFlash()) { Functions::includeFile(file: '/app/Views/components/flash.php'); } ?>

<section id="content">
    <div class="flex flex-row h-screen px-[30px] pt-[71px] pb-[71px] select-text layout-container">
        <div class="w-full overflow-y-auto">
            {{content}}
        </div>
    </div>
</section>

<script src="/js/theme-footer.js"></script>

</body>
</html>
