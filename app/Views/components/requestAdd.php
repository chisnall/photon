<?php
use App\Core\Application;
use App\Functions\Data;

// Get requests data for this user
$sql = "SELECT requests.id, requests.id as request_id, requests.request_name, collections.id as collection_id, collections.collection_name FROM requests JOIN collections on requests.collection_id = collections.id WHERE collections.user_id = " . Application::app()->user()->id() . " ORDER BY collections.collection_name ASC, requests.request_name ASC, requests.request_method, requests.created_at";
$requestsData = Data::records($sql);
$requestsCount = count($requestsData);

// Get existing requests
$groupRequests = Application::app()->session()->get('tests/upper/groupRequests');

// Remove existing requests from available requests
if ($groupRequests) {
    foreach ($groupRequests as $groupRequest) {
        unset($requestsData[$groupRequest['id']]);
    }
}
?>

<?php if ($requestsData): ?>
    <table>
        <tr class="h-8">
            <td class="min-w-30 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Collection</td>
            <td class="min-w-12 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">ID</td>
            <td class="w-full px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Request</td>
            <td class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></td>
        </tr>
    <?php foreach ($requestsData as $requestData): ?>
        <?php
        $requestId = $requestData['request_id'];
        $requestName = $requestData['request_name'];
        $collectionName = $requestData['collection_name'];
        ?>
        <tr class="h-8">
            <td class="whitespace-nowrap align-top px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                <?= $collectionName ?>
            </td>
            <td class="align-top text-right px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                <?= $requestId ?>
            </td>
            <td class="align-top px-1 py-1 border border-zinc-300 dark:border-zinc-650 break-all">
                <?= $requestName ?>
            </td>
            <td class="align-top px-1 py-0 border border-zinc-300 dark:border-zinc-650">
                <label class="toggle mt-1 -mb-1">
                    <input name="groupRequestsAdd[]" type="checkbox" value="<?= $requestId ?>">
                    <span class="toggle round"></span>
                </label>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php elseif ($requestsCount > 0): ?>
    <div>No requests are available.</div>
<?php else: ?>
    <div>No requests have been created yet.</div>
<?php endif; ?>
<input type="hidden" name="requestsCount" value="<?= count($requestsData) ?>">

<script>
// Disable submit button if no requests are available
var requestsCount = $("input[name='requestsCount']").val();
if (requestsCount === "0") {
    $("form#requestAdd button[type='submit']").prop("disabled", true);
} else {
    $("form#requestAdd button[type='submit']").prop("disabled", false);
}
</script>
