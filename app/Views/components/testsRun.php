<?php
/**  @var int $groupId **/

declare(strict_types=1);

// Get tests progress
$progressFile = "/var/lib/photon/output/groupTests-$groupId-progress";
if (file_exists($progressFile)) {
    $progressOutput = trim(file_get_contents($progressFile));
    $progressOutputArray = explode("\n", $progressOutput);
} else {
    $progressOutput = null;
    $progressOutputArray = [];
}
?>

<?php if ($progressOutput == 'none'): ?>
    <div>No requests to run.</div>
<?php //elseif (count($progressOutputArray) > 0): ?>
<?php elseif ($progressOutput): ?>
    <table class="table-auto text-left text-sm">
        <tr class="h-8">
            <th class="min-w-10 px-2 py-0 text-right font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">ID</th>
            <th class="min-w-20 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Method</th>
            <th class="min-w-60 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Request</th>
            <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Valid</th>
            <th class="px-2 py-0 text-right font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Tests</th>
        </tr>
    <?php foreach ($progressOutputArray as $progressOutputLine): ?>
        <?php
        $requestInfo = explode(',', $progressOutputLine, 5);
        $requestValid = $requestInfo[0];
        $requestId = $requestInfo[1];
        $requestTests = $requestInfo[2];
        $requestMethod = $requestInfo[3];
        $requestName = $requestInfo[4];

        $requestValid ? $requestValidDisplay = '<i class="text-green-700 dark:text-lime-700 fa-solid fa-circle-check"></i>' : $requestValidDisplay = '<i class="text-red-600 dark:text-red-700 fa-solid fa-circle-xmark"></i>';
        ?>
        <tr class="h-8">
            <td class="px-2 text-right border border-zinc-300 dark:border-zinc-650"><?= $requestId ?></td>
            <td class="px-2 font-semibold border border-zinc-300 dark:border-zinc-650 dropdown-method-<?= strtolower($requestMethod) ?>"><?= $requestMethod ?></td>
            <td class="px-2 border border-zinc-300 dark:border-zinc-650"><?= $requestName ?></td>
            <td class="px-2 text-center text-[120%] align-middle border border-zinc-300 dark:border-zinc-650"><?= $requestValidDisplay ?></td>
            <td class="px-2 text-right border border-zinc-300 dark:border-zinc-650"><?= $requestTests ?></td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php else: ?>
    <div>Wait...</div>
<?php endif; ?>
