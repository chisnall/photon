<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Functions;
use App\Functions\Data;
use App\Functions\Css;
use App\Functions\Output;
use App\Models\GroupModel;
use App\Models\SettingsModel;
use App\Models\UserModel;

// Get groups data and selected collection
$groupsData = GroupModel::getAllRecords(match: ['userId' => Application::app()->user()->id()], sort: ['groupName' => 'ASC']);
$left_groupId = Application::app()->session()->get('tests/left/groupId');
$left_groupName = Application::app()->session()->get('tests/left/groupName');

// Get requests data for this user
$sql = "SELECT requests.id, requests.id as request_id, requests.request_name, collections.id as collection_id, collections.collection_name FROM requests JOIN collections on requests.collection_id = collections.id WHERE collections.user_id = " . Application::app()->user()->id() . " ORDER BY collections.collection_name ASC, requests.request_name ASC, requests.request_method, requests.created_at";
$requestsData = Data::records($sql);

// Get input values from session
// ?: covers both null and empty "" values
$groupName = Application::app()->session()->get('tests/upper/groupName');
$groupRequests = Application::app()->session()->get('tests/upper/groupRequests') ?? [];

// Remove orphan requests (requests that have been deleted but are still present in the group requests array)
foreach ($groupRequests as $groupRequestKey => $groupRequestValue) {
    if (!array_key_exists($groupRequestValue['id'], $requestsData)) unset($groupRequests[$groupRequestKey]);
}

// Set title
$left_groupName !== null ? $title = "Tests: $left_groupName" : $title = 'Tests';

// Get open modal
$modalOpenName = $_POST['modalName'] ?? null;

// Get overlay class
$modalOverlayClass = Css::getOverlayClass();

// Get form error
$groupError = Application::app()->session()->get('tests/upper/groupError');

// Get tabs from session
$left_selectedTab = Application::app()->session()->get('tests/left/selectedTab') ?? "tab1";
$upper_selectedTab = Application::app()->session()->get('tests/upper/selectedTab') ?? "tab1";
$lower_selectedTab = Application::app()->session()->get('tests/lower/selectedTab') ?? "tab1";

// Set tabs CSS classes
$left_selectedTab == 'tab1' ? $left_selectedTab_tab1 = ' current' : $left_selectedTab_tab1 = null;
$left_selectedTab == 'tab2' ? $left_selectedTab_tab2 = ' current' : $left_selectedTab_tab2 = null;
$upper_selectedTab == 'tab1' ? $upper_selectedTab_tab1 = ' current' : $upper_selectedTab_tab1 = null;
$upper_selectedTab == 'tab2' ? $upper_selectedTab_tab2 = ' current' : $upper_selectedTab_tab2 = null;
$lower_selectedTab == 'tab1' ? $lower_selectedTab_tab1 = ' current' : $lower_selectedTab_tab1 = null;
$lower_selectedTab == 'tab2' ? $lower_selectedTab_tab2 = ' current' : $lower_selectedTab_tab2 = null;

// Get settings
$settings_tests_leftSection = Application::app()->session()->get('tests/layout/leftSection') ?? SettingsModel::getSetting('tests/layout/leftSection');
$settings_tests_rightSection = Application::app()->session()->get('tests/layout/rightSection') ?? SettingsModel::getSetting('tests/layout/rightSection');
$settings_tests_topSection = Application::app()->session()->get('tests/layout/topSection') ?? SettingsModel::getSetting('tests/layout/topSection');
$settings_tests_bottomSection = Application::app()->session()->get('tests/layout/bottomSection') ?? SettingsModel::getSetting('tests/layout/bottomSection');
//---
$settings_groups_stopOnResponseFail = SettingsModel::getSetting('groups/stopOnResponseFail');

// Settings - display variables
$settings_groups_stopOnResponseFail ? $display_groups_stopOnResponseFail = 'On' : $display_groups_stopOnResponseFail = 'Off';
?>
<style>
    pre.sf-dump { width: 100%; }
</style>

<div id="pageSection" class="fixed top-0 flex h-screen w-screen flex-row pt-[41px] pb-[41px]">

    <div id="leftSection" class="layout-container flex flex-col" style="width: <?= $settings_tests_leftSection ?>;">
        <div id="left-header" class="ml-5 mr-3 mt-5 mb-5">
            <div id="tab1-header" class="tab-header<?= $left_selectedTab_tab1 ?>">
                <div class="flex flex-row justify-between items-start">
                    <div class="font-bold">Groups <?php if (count($groupsData) > 0) echo '(' . count($groupsData) . ')' ?></div>
                    <?php if (UserModel::isLoggedIn()): ?>
                        <div class="flex flex-row items-center">
                            <?php if ($left_groupId): ?>
                                <div class="px-2 mr-2 rounded-lg text-white bg-green-700 dark:bg-lime-700"><?= $left_groupId ?></div>
                                <a href="/tests?unselect=group"><button type="button" name="requestUnselect" class="general">Unselect</button></a>
                            <?php else: ?>
                                <button type="button" name="requestUnselect" class="general" disabled>Unselect</button>
                            <?php endif; ?>
                            <button type="button" name="groupCreateOpenButton" data-modal="groupCreateModal" data-input_group-name="" data-focus="groupName" class="general ml-2"><span><i class="fa-solid fa-plus"></i> Create</span></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="tab2-header" class="tab-header<?= $left_selectedTab_tab2 ?>">
                <div class="font-bold">Tab 2</div>
            </div>
        </div>

        <div class="overflow-hidden flex flex-col h-full ml-5 mr-3">
            <div id="left-content" class="overflow-y-auto flex flex-col h-full">

                <div id="tab1-content" class="tab-content<?= $left_selectedTab_tab1 ?>">
                <?php if (UserModel::isLoggedIn()): ?>
                    <?php if (count($groupsData) > 0): ?>

                        <table id="groupsList">
                        <?php foreach ($groupsData as $groupModel): ?>
                            <tr id="<?= $groupModel->getProperty('id') ?>" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 cursor-pointer<?php if ($groupModel->getProperty('id') == $left_groupId) { echo ' bg-zinc-100 dark:bg-zinc-800'; } ?>">
                                <td class="pl-2 pr-1 py-1 text-xs text-zinc-500 dark:text-zinc-400 text-right"><?= $groupModel->getProperty('id') ?></td>
                                <td class="w-full px-1 py-1"><?= $groupModel->getProperty('groupName') ?></td>
                                <?php if ($groupModel->getProperty('id') == $left_groupId): ?>
                                    <td class="px-2 py-1 text-right text-[75%] text-red-600 dark:text-red-700"><i id="groupModified" class="fa-solid fa-circle<?php if (!Application::app()->session()->get('tests/upper/groupModified')) echo ' hidden' ?>"></i></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </table>

                    <?php else: ?>
                        <div>No groups have been created yet.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div>Login to manage groups.</div>
                <?php endif; ?>
                </div>

                <div id="tab2-content" class="tab-content<?= $left_selectedTab_tab2 ?>">
                    Tab 2 content
                </div>

            </div>
        </div>

        <div class="-mr-2 border-t border-zinc-300 dark:border-zinc-650">
            <div class="ml-4 mr-3 mt-4 mb-4">
                <div>
                    <ul id="left" class="tabs">
                        <li id="tab1" class="w-[84px] tab-link<?= $left_selectedTab_tab1 ?>"><div class="flex flex-col items-center"><div><?= Output::icon('groups') ?></div><div>Groups</div></div></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div id="borderVertical" class="borderVertical borderVerticalHover">
        <div id="borderVerticalLeft" class="borderVerticalLeft"></div>
        <div id="borderVerticalRight" class="borderVerticalRight"></div>
    </div>

    <div id="rightSection" class="flex flex-col" style="width: <?= $settings_tests_rightSection ?>;">

        <div id="topSection" class="layout-container flex flex-col" style="height: <?= $settings_tests_topSection ?>;">
            <form id="groupManage" action="/tests" method="POST" class="flex flex-col h-full">

                <div class="ml-3 mr-5 mt-5 mb-3">

                    <div class="flex">
                        <div class="flex w-[55%]">
                            <div class="mr-4">
                            <?php if ($left_groupId): ?>
                                <button type="button" name="testsRunOpenButton" data-modal="testsRunModal" class="w-[90px] h-[43px] text-[14px] px-6 py-2 rounded-lg font-bold text-white border bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 border-red-800 dark:border-red-900">Run</button>
                            <?php else: ?>
                                <button type="button" class="w-[90px] h-[43px] text-[14px] px-6 py-2 rounded-lg font-bold text-red-400 dark:text-red-500 border bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 border-red-800 dark:border-red-900 cursor-not-allowed" disabled>Run</button>
                            <?php endif; ?>
                            </div>

                            <div class="grow">
                                <input type="text" id="groupName" name="groupName" data-action="save" placeholder="Enter name" autocomplete="one-time-code" spellcheck="false" oninvalid="this.setCustomValidity('Enter the group name')" oninput="this.setCustomValidity('')" value="<?= $groupName ?>" class="w-full h-[43px] p-2 rounded-tl-lg rounded-bl-lg border border-r-0 border-zinc-300 dark:border-zinc-650 focus:ring-0 focus:border-zinc-300 dark:focus:border-zinc-650 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"<?php if (!$left_groupId) echo ' disabled'; ?>>
                            </div>

                            <?php if ($left_groupId): ?>
                                <div>
                                    <button type="button" id="saveSubmitButton" value="save" class="h-[43px] text-[20px] px-4 pb-[2px] border bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600"><i class="fa-solid fa-floppy-disk"></i></button>
                                </div>
                                <div>
                                    <button type="button" name="groupCloneOpenButton" data-modal="groupCloneModal" data-input_group-name="" data-focus="groupName" class="h-[43px] text-[20px] px-4 pb-[2px] border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600"><i class="fa-solid fa-clone"></i></button>
                                </div>
                                <div>
                                    <button type="button" name="groupDeleteOpenButton" data-modal="groupDeleteModal" class="h-[43px] text-[20px] px-4 pb-[2px] rounded-tr-lg rounded-br-lg border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600"><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                                <?php else: ?>
                                <div>
                                    <button type="button" class="h-[43px] text-[20px] px-4 pb-[2px] text-zinc-400 dark:text-zinc-500 border bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600 cursor-not-allowed" disabled><i class="fa-solid fa-floppy-disk"></i></button>
                                </div>
                                <div>
                                    <button type="button" class="h-[43px] text-[20px] px-4 pb-[2px] text-zinc-400 dark:text-zinc-500 border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600 cursor-not-allowed" disabled><i class="fa-solid fa-clone"></i></button>
                                </div>
                                <div>
                                    <button type="button" class="h-[43px] text-[20px] px-4 pb-[2px] text-zinc-400 dark:text-zinc-500 rounded-tr-lg rounded-br-lg border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600 cursor-not-allowed" disabled><i class="fa-solid fa-trash-can"></i></button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center ml-4">
                        <?php if ($groupError): ?>
                            <div id="groupError" class="font-bold text-red-600 dark:text-red-700"><i class="fa-solid fa-circle-exclamation"></i> <?= $groupError ?></div>
                        <?php endif; ?>
                        </div>
                    </div>

                    <input type="hidden" name="modelClassName" value="GroupModel">
                    <input type="hidden" name="id" value="<?= $left_groupId ?>">
                    <input type="hidden" name="userId" value="<?= Application::app()->user()->id() ?>">
                    <input type="hidden" name="formAction" value="">
                    <input type="hidden" name="left_selectedTab" value="<?= $left_selectedTab ?>">
                    <input type="hidden" name="upper_selectedTab" value="<?= $upper_selectedTab ?>">
                    <input type="hidden" name="lower_selectedTab" value="<?= $lower_selectedTab ?>">
                    <input type="hidden" name="modalOpenName" value="<?= $modalOpenName ?>">

                    <div class="mt-5">
                        <div class="flex flex-row items-center justify-between h-[39px]">
                            <div>
                                <ul id="upper" class="tabs">
                                    <li id="tab1" class="tab-link<?= $upper_selectedTab_tab1 ?>">Requests</li>
                                    <li id="tab2" class="tab-link<?= $upper_selectedTab_tab2 ?>">Settings</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="overflow-hidden flex flex-col h-full ml-3 mr-5 mt-0 mb-3">
                    <div id="upper-content" class="overflow-y-auto flex flex-col h-full">
                        <div id="tab1-content" class="tab-content<?= $upper_selectedTab_tab1 ?>">
                        <?php if (count($groupRequests) > 0): ?>
                            <div>
                                <table id="groupRequests" class="table-auto text-left text-sm">
                                    <thead>
                                    <tr class="h-8">
                                        <th class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        <th class="min-w-30 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Collection</th>
                                        <th class="min-w-10 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">ID</th>
                                        <th class="min-w-60 px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Request</th>
                                        <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($groupRequests as $groupRequest): ?>
                                        <?php
                                        $groupRequestId = $groupRequest['id'];
                                        $groupRequestEnabled = $groupRequest['enabled'];
                                        $groupRequestName = $requestsData[$groupRequestId]['request_name'];
                                        $groupRequestCollectionName = $requestsData[$groupRequestId]['collection_name'];
                                        $groupRequestEnabled == 'on' ? $groupRequestEnabledCheckbox = ' checked' : $groupRequestEnabledCheckbox = null;
                                        ?>
                                        <tr class="h-8">
                                            <td class="w-10 pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                <input type="checkbox" name="groupRequestCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $groupRequestEnabledCheckbox ?>>
                                                <input type="hidden" name="groupRequestId[]" value="<?= $groupRequestId ?>">
                                                <input type="hidden" name="groupRequestEnabled[]" value="<?= $groupRequestEnabled ?>">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <?= $groupRequestCollectionName ?>
                                            </td>
                                            <td class="text-right px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <?= $groupRequestId ?>
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <?= $groupRequestName ?>
                                            </td>
                                            <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <button type="button" name="deleteButton" class="px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                            </td>
                                            <td class="dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                                <div id="dragHandle">
                                                    <i class="fa-solid fa-grip-vertical"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div id="groupRequests"></div>
                            </div>
                        <?php elseif ($left_groupId): ?>
                            <div>No requests have been added yet.</div>
                        <?php else: ?>
                            <div>Select a group to manage requests.</div>
                        <?php endif; ?>
                        </div>

                        <div id="tab2-content" class="tab-content<?= $upper_selectedTab_tab2 ?>">
                            <div class="mr-5">
                                <table class="text-left border-collapse text-sm">
                                    <tr>
                                        <th class="table-heading">Setting</th>
                                        <th class="table-heading">Value</th>
                                    </tr>
                                    <tr>
                                        <td class="table-cell">Stop on response fail</td>
                                        <td class="table-cell"><?= $display_groups_stopOnResponseFail ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="upper-footer">

                    <div id="tab1-footer" class="ml-3 mr-5 mt-2 mb-3 tab-footer<?= $upper_selectedTab_tab1 ?>">
                        <div>
                            <button type="button" name="requestAddOpenButton" data-modal="requestAddModal" class="general"<?php if (!$left_groupId) echo ' disabled'; ?>><span><i class="fa-solid fa-plus"></i> Add</span></button>
                        </div>
                    </div>

                    <div id="tab2-footer" class="ml-3 mr-5 mt-2 mb-3 tab-footer<?= $upper_selectedTab_tab2 ?>">
                    <?php if (UserModel::isLoggedIn()): ?>
                        <div>
                            <a href="/?select=settings&tab=tab3" target="_blank"><button type="button" class="general"><span><i class="fa-solid fa-sliders mr-2"></i> Update settings</span></button></a>
                        </div>
                    <?php else: ?>
                        <div>Login to update settings.</div>
                    <?php endif; ?>
                    </div>

                </div>

            </form>
        </div>

        <div id="borderHorizontal" class="borderHorizontal borderHorizontalHover">
            <div id="borderHorizontalTop" class="borderHorizontalTop"></div>
            <div id="borderHorizontalBottom" class="borderHorizontalBottom"></div>
        </div>

        <div id="bottomSection" class="layout-container flex flex-col" style="height: <?= $settings_tests_bottomSection ?>;">

            <div class="ml-3 mr-5 mt-3 mb-5">
                <div class="flex flex-row items-center justify-between h-[39px]">

                    <div>
                        <ul id="lower" class="tabs">
                            <li id="tab1" class="tab-link<?= $lower_selectedTab_tab1 ?>">Summary</li>
                            <li id="tab2" class="tab-link<?= $lower_selectedTab_tab2 ?>">Results</li>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="overflow-hidden flex flex-col h-full ml-3 mr-5 mt-0 mb-5">
                <div id="lower-content" class="overflow-y-auto flex flex-col h-full">

                    <div id="tab1-content" class="tab-content<?= $lower_selectedTab_tab1 ?>">
                        <?php Functions::includeFile(file: '/app/Views/components/testsSummary.php', variables: ['groupId' => $left_groupId]); ?>
                    </div>

                    <div id="tab2-content" class="tab-content<?= $lower_selectedTab_tab2 ?>">
                        <?php Functions::includeFile(file: '/app/Views/components/testsResults.php', variables: ['groupId' => $left_groupId]); ?>
                    </div>

                </div>
            </div>

            <div id="lower-footer">
            </div>

        </div>
    </div>

</div>

<?php Functions::includeFile(file: '/app/Views/modals/groupCloneModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/groupCreateModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/groupDeleteModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/requestAddModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/testsRunModal.php'); ?>

<div id="modalOverlay" class="<?= $modalOverlayClass ?>"></div>

<script>
    var ajaxToken = $("input#ajaxToken").val();
    var formId = "groupManage";
</script>
<script src="/js/modal.js"></script>
<script src="/js/tablednd.js"></script>
<script src="/js/tabs.js"></script>
<script src="/js/tests-main.js"></script>
<script src="/js/tests-run.js"></script>
<script>
<?php
// Check if user is guest or settings have been set to defaults
if (str_ends_with($settings_tests_leftSection, '%')) {
    echo "var initGuest = true;\n";
} else {
    echo "var initGuest = false;\n";
}

// Check if user has logged in
if (Application::app()->session()->get('tests/layout/initUser')) {
    Application::app()->session()->set("tests/layout/initUser", false);
    echo "var initUser = true;\n";
} else {
    echo "var initUser = false;\n";
}
?>
</script>
<script src="/js/borders.js"></script>
