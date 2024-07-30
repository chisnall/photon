<?php
use App\Core\Application;

$flashClass = Application::app()->session()->getFlash()['class'];
$flashMessage = Application::app()->session()->getFlash()['message'];
?>
<div id="flash-msg" class="<?= $flashClass ?> fixed top-0 z-[100] left-1/2 -translate-x-1/2 px-48 py-4 rounded-b-xl font-semibold">
    <p><?= $flashMessage ?></p>
</div>
<script>
    setTimeout(function() {
        $('#flash-msg').fadeOut('slow');
    }, 2000);
</script>
