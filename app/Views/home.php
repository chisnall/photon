<?php

use App\Core\Application;
use App\Core\Functions;
use App\Functions\Css;
use App\Functions\Json;
use App\Functions\Output;
use App\Models\CollectionModel;
use App\Models\RequestModel;
use App\Models\SettingsModel;
use App\Models\TestModel;
use App\Models\UserModel;

// Get collections data and selected collection
$collectionsData = CollectionModel::getAllRecords(match: ['userId' => Application::app()->user()->id()], sort: ['collectionName' => 'ASC']);
$left_collectionId = Application::app()->session()->get('home/left/collectionId');
$left_collectionName = Application::app()->session()->get('home/left/collectionName');

// Get requests data and selected request
$left_collectionId ? $requestsData = RequestModel::getAllRecords(match: ['collectionId' => $left_collectionId], sort: ['requestName' => 'ASC', 'requestMethod' => 'ASC', 'createdAt' => 'ASC']) : $requestsData = [];
$left_requestId = Application::app()->session()->get('home/left/requestId');
$left_requestName = Application::app()->session()->get('home/left/requestName');

// Get collection ID of request - this may be different than the current selected collection
$left_requestId ? $left_requestCollectionId = RequestModel::getSingleRecord(['id' => $left_requestId])->getProperty('collectionId') : $left_requestCollectionId = null;

// Get tests data
$left_requestId ? $testsData = TestModel::getAllRecords(match: ['requestId' => $left_requestId], sort: ['testName' => 'ASC']) : $testsData = [];

// Get input values from session
// ?: covers both null and empty "" values
$requestMethod = Application::app()->session()->get('home/upper/requestMethod') ?: "get";
$requestUrl = Application::app()->session()->get('home/upper/requestUrl');
$requestName = Application::app()->session()->get('home/upper/requestName');
$requestParamsInputs = Application::app()->session()->get('home/upper/requestParamsInputs') ?? [];
$requestHeadersInputs = Application::app()->session()->get('home/upper/requestHeadersInputs') ?? [];
$requestAuth = Application::app()->session()->get('home/upper/requestAuth') ?: "none";
$requestAuthBasicUsername = Application::app()->session()->get('home/upper/requestAuthBasicUsername') ?: null;
$requestAuthBasicPassword = Application::app()->session()->get('home/upper/requestAuthBasicPassword') ?: null;
$requestAuthTokenValue = Application::app()->session()->get('home/upper/requestAuthTokenValue') ?: null;
$requestAuthHeaderName = Application::app()->session()->get('home/upper/requestAuthHeaderName') ?: null;
$requestAuthHeaderValue = Application::app()->session()->get('home/upper/requestAuthHeaderValue') ?: null;
$requestBody = Application::app()->session()->get('home/upper/requestBody') ?: "none";
$requestBodyTextValue = Application::app()->session()->get('home/upper/requestBodyTextValue') ?: null;
$requestBodyTextType = Application::app()->session()->get('home/upper/requestBodyTextType') ?: "json";
$requestBodyFormInputs = Application::app()->session()->get('home/upper/requestBodyFormInputs') ?? [];
$requestBodyFileExisting = Application::app()->session()->get('home/upper/requestBodyFileExisting');
$requestVariablesInputs = Application::app()->session()->get('home/upper/requestVariablesInputs') ?? [];

// Set title
$requestUrl !== null ? $title = strtoupper($requestMethod) . ' ' . $requestUrl : $title = 'Home';

// Get open modal
$modalOpenName = $_POST['modalName'] ?? null;

// Get overlay class
$modalOverlayClass = Css::getOverlayClass();

// Get form error
$requestError = Application::app()->session()->get('tests/upper/requestError');

// Get tabs from session
$left_selectedTab = Application::app()->session()->get('home/left/selectedTab') ?? "tab1";
$upper_selectedTab = Application::app()->session()->get('home/upper/selectedTab') ?? "tab1";
$lower_selectedTab = Application::app()->session()->get('home/lower/selectedTab') ?? "tab1";

// Set tabs CSS classes
$left_selectedTab == 'tab1' ? $left_selectedTab_tab1 = ' current' : $left_selectedTab_tab1 = null;
$left_selectedTab == 'tab2' ? $left_selectedTab_tab2 = ' current' : $left_selectedTab_tab2 = null;
$left_selectedTab == 'tab3' ? $left_selectedTab_tab3 = ' current' : $left_selectedTab_tab3 = null;
$upper_selectedTab == 'tab1' ? $upper_selectedTab_tab1 = ' current' : $upper_selectedTab_tab1 = null;
$upper_selectedTab == 'tab2' ? $upper_selectedTab_tab2 = ' current' : $upper_selectedTab_tab2 = null;
$upper_selectedTab == 'tab3' ? $upper_selectedTab_tab3 = ' current' : $upper_selectedTab_tab3 = null;
$upper_selectedTab == 'tab4' ? $upper_selectedTab_tab4 = ' current' : $upper_selectedTab_tab4 = null;
$upper_selectedTab == 'tab5' ? $upper_selectedTab_tab5 = ' current' : $upper_selectedTab_tab5 = null;
$upper_selectedTab == 'tab6' ? $upper_selectedTab_tab6 = ' current' : $upper_selectedTab_tab6 = null;
$upper_selectedTab == 'tab7' ? $upper_selectedTab_tab7 = ' current' : $upper_selectedTab_tab7 = null;
$lower_selectedTab == 'tab1' ? $lower_selectedTab_tab1 = ' current' : $lower_selectedTab_tab1 = null;
$lower_selectedTab == 'tab2' ? $lower_selectedTab_tab2 = ' current' : $lower_selectedTab_tab2 = null;
$lower_selectedTab == 'tab3' ? $lower_selectedTab_tab3 = ' current' : $lower_selectedTab_tab3 = null;
$lower_selectedTab == 'tab4' ? $lower_selectedTab_tab4 = ' current' : $lower_selectedTab_tab4 = null;
$lower_selectedTab == 'tab5' ? $lower_selectedTab_tab5 = ' current' : $lower_selectedTab_tab5 = null;
$lower_selectedTab == 'tab6' ? $lower_selectedTab_tab6 = ' current' : $lower_selectedTab_tab6 = null;

// Set auth and body content
$requestAuth == 'none' ? $upper_selectedTab_tab3_none = ' current' : $upper_selectedTab_tab3_none = null;
$requestAuth == 'basic' ? $upper_selectedTab_tab3_basic = ' current' : $upper_selectedTab_tab3_basic = null;
$requestAuth == 'token' ? $upper_selectedTab_tab3_token = ' current' : $upper_selectedTab_tab3_token = null;
$requestAuth == 'header' ? $upper_selectedTab_tab3_header = ' current' : $upper_selectedTab_tab3_header = null;
$requestBody == 'none' ? $upper_selectedTab_tab4_none = ' current' : $upper_selectedTab_tab4_none = null;
$requestBody == 'text' ? $upper_selectedTab_tab4_text = ' current' : $upper_selectedTab_tab4_text = null;
$requestBody == 'form' ? $upper_selectedTab_tab4_form = ' current' : $upper_selectedTab_tab4_form = null;
$requestBody == 'file' ? $upper_selectedTab_tab4_file = ' current' : $upper_selectedTab_tab4_file = null;

// Show/hide body text type button
$requestBody == 'text' ? $requestBodyClass = null : $requestBodyClass = 'hidden ';

// Get settings
$settings_home_leftSection = Application::app()->session()->get('home/layout/leftSection') ?? SettingsModel::getSetting('home/layout/leftSection');
$settings_home_rightSection = Application::app()->session()->get('home/layout/rightSection') ?? SettingsModel::getSetting('home/layout/rightSection');
$settings_home_topSection = Application::app()->session()->get('home/layout/topSection') ?? SettingsModel::getSetting('home/layout/topSection');
$settings_home_bottomSection = Application::app()->session()->get('home/layout/bottomSection') ?? SettingsModel::getSetting('home/layout/bottomSection');
//---
$settings_http_defaultScheme = SettingsModel::getSetting('http/defaultScheme');
$settings_http_timeout = SettingsModel::getSetting('http/timeout');
$settings_http_sortHeaders = SettingsModel::getSetting('http/sortHeaders');
$settings_http_version = SettingsModel::getSetting('http/version');
$settings_http_accept = SettingsModel::getSetting('http/accept');
//---
$settings_json_lineNumbers = SettingsModel::getSetting('json/lineNumbers');
$settings_json_indent = SettingsModel::getSetting('json/indent');
$settings_json_linkUrls = SettingsModel::getSetting('json/linkUrls');
$settings_json_trailingCommas = SettingsModel::getSetting('json/trailingCommas');
$settings_json_quoteKeys = SettingsModel::getSetting('json/quoteKeys');
//---
$settings_variables_showGlobalsHome = SettingsModel::getSetting('variables/showGlobalsHome');

// Settings - display variables
$display_http_defaultScheme = $settings_http_defaultScheme;
$display_http_timeout = number_format($settings_http_timeout, 1);
$settings_http_sortHeaders === true ? $display_http_sortHeaders = 'On' : $display_http_sortHeaders = 'Off';
$display_http_version = ucfirst($settings_http_version);
$settings_http_accept == 'default' ? $display_http_accept = "Default" : $display_http_accept = $settings_http_accept;
$display_json_lineNumbers = ucfirst($settings_json_lineNumbers);
$display_json_indent = $settings_json_indent;
$settings_json_trailingCommas === true ? $display_json_trailingCommas = 'On' : $display_json_trailingCommas = 'Off';
$settings_json_quoteKeys === true ? $display_json_quoteKeys = 'On' : $display_json_quoteKeys = 'Off';
$settings_json_linkUrls === true ? $display_json_linkUrls = 'On' : $display_json_linkUrls = 'Off';

// Cast boolean values to string for home-json.js
$settings_json_linkUrls = json_encode($settings_json_linkUrls);
$settings_json_trailingCommas = json_encode($settings_json_trailingCommas);
$settings_json_quoteKeys = json_encode($settings_json_quoteKeys);

// Get variables
if ($left_collectionId) {
    // Get global variables
    $settings_variables_showGlobalsHome ? $globalVariablesData = SettingsModel::variables(Application::app()->user()->id()) : $globalVariablesData = [];

    // Get collection and request variables
    $collectionVariablesData = CollectionModel::variables($left_collectionId);
    $requestVariablesData = Application::app()->session()->get("variables/$left_collectionId") ?? [];

    // Merge arrays - give priority to the request variables where keys clash, followed by collection variables
    $variablesData = array_merge($globalVariablesData, $collectionVariablesData, $requestVariablesData);

    ksort($variablesData, SORT_NATURAL);
} else {
    $variablesData = [];
}

// HTTP method
$httpMethodList = [
    "get" => "GET",
    "head" => "HEAD",
    "post" => "POST",
    "put" => "PUT",
    "patch" => "PATCH",
    "delete" => "DELETE",
    "options" => "OPTIONS"
];

// HTTP auth
$httpAuthList = [
    "none" => 'None',
    "basic" => 'Basic',
    "token" => 'Token',
    "header" => 'Header',
];
$httpAuthListIcons = [
    "none" => '<span class="inline-block w-5 font-bold text-[125%] relative top-[1px] leading-[85%]" style="position: relative;">Ø</span>',
    "basic" => '<span class="inline-block w-5"><i class="fa-solid fa-user-large"></i></span>',
    "token" => '<span class="inline-block w-5"><i class="fa-solid fa-key"></i></span>',
    "header" => '<span class="inline-block w-5"><i class="fa-solid fa-tag fa-flip-horizontal"></i></span>',
];

// HTTP body
$httpBodyList = [
    "none" => 'None',
    "text" => 'Text',
    "form" => 'Form',
    "file" => 'File',
];
$httpBodyListIcons = [
    "none" => '<span class="inline-block w-5 font-bold text-[125%] relative top-[1px] leading-[85%]" style="position: relative;">Ø</span>',
    "text" => '<span class="inline-block w-5"><i class="fa-solid fa-align-left"></i></span>',
    "form" => '<span class="inline-block w-5"><i class="fa-solid fa-table-cells-large"></i></span>',
    "file" => '<span class="inline-block w-5"><i class="fa-solid fa-paperclip"></i></span>',
];

// HTTP body text type
$httpBodyTextTypeList = [
    "plain" => 'Plain',
    "json" => 'JSON',
    "html" => 'HTML',
    "xml" => 'XML',
    "yaml" => 'YAML',
];

// Get uploaded body files
$uploadedBodyFilesDirectory = UPLOAD_PATH . '/' . Application::app()->user()->id();
if (file_exists($uploadedBodyFilesDirectory)) {
    $uploadedBodyFiles = array_diff(scandir($uploadedBodyFilesDirectory), ['.', '..']);
    natcasesort($uploadedBodyFiles);
} else {
    $uploadedBodyFiles = null;
}

// Set default empty clipboard title
$clipboardTitle = "No data";

// Get response data
$responseRequestTime = Application::app()->session()->get('response/responseRequestTime');
$responseValid = Application::app()->session()->get('response/responseValid');
$responseException = Application::app()->session()->get('response/responseException');
$responseExceptionClass = Application::app()->session()->get('response/responseExceptionClass');
$responseScheme = Application::app()->session()->get('response/responseScheme');
$responseSchemeIcon = Application::app()->session()->get('response/responseSchemeIcon');
$responseCode = Application::app()->session()->get('response/responseCode');
$responseType = Application::app()->session()->get('response/responseType');
$responseStatusLine = Application::app()->session()->get('response/responseStatusLine');
$responseStatusProtocol = Application::app()->session()->get('response/responseStatusProtocol');
$responseStatusCode = Application::app()->session()->get('response/responseStatusCode');
$responseStatusText = Application::app()->session()->get('response/responseStatusText');
$responseHeaders = Application::app()->session()->get('response/responseHeaders');
$responseBodyContent = Application::app()->session()->get('response/responseBodyContent');
$responseBodyDecoded = Application::app()->session()->get('response/responseBodyDecoded');
$responseBodySize = Application::app()->session()->get('response/responseBodySize');
$responseBodySizeFormatted = Application::app()->session()->get('response/responseBodySizeFormatted');
$responseBodyValid = Application::app()->session()->get('response/responseBodyValid');
$responseTime = Application::app()->session()->get('response/responseTime');
$responseTimeFormatted = Application::app()->session()->get('response/responseTimeFormatted');
$responseErrorMessage = Application::app()->session()->get('response/responseErrorMessage');
$testsResults = Application::app()->session()->get('response/testsResults');

// Init variables
$responseRequestAge = null;
$responseRequestAgeDisplay = null;
$responseHeadersClipboard = null;
$responseBodySafe = null;
$responseBodySafeArray = null;
$responseBodyEncoded = null;
$responseBodyClipboard = null;
$responseBodyRawClipboard = null;
$responseErrorShort = null;
$responseErrorLong = null;

// Check for response error message
if ($responseErrorMessage) {
    // Process error message
    $responseErrorLong = preg_replace('/: .*/', '', $responseErrorMessage);
}

// Check for valid response
if ($responseValid) {
    // Sort headers
    if ($settings_http_sortHeaders) {
        ksort($responseHeaders, SORT_NATURAL);
    }

    // Format request age
    $responseRequestAgeDisplay = Output::ageFormat($responseRequestTime);

    // Headers for clipboard
    $responseHeadersClipboard =  implode("\n", array_map(function ($a, $b) { return "$a: $b"; },
        array_keys($responseHeaders), array_values($responseHeaders)));

    // Check for body
    if ($responseBodyContent) {
        // Make body safe for display - it could contain HTML and this will break the page output
        $responseBodySafe = htmlspecialchars($responseBodyContent);

        // Array for safe output
        $responseBodySafeArray = explode("\n", $responseBodySafe);
    }

    // Check for valid JSON
    if ($responseBodyValid === true) {
        // Encode body - this is for the Body tab
        $responseBodyEncoded = json_encode($responseBodyDecoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        // Check indentation setting other than default
        if ($settings_json_indent != 4) {
            $responseBodyEncoded = Json::format($responseBodyEncoded, $settings_json_indent);
        }

        // Set clipboard
        $responseBodyClipboard = $responseBodyEncoded;
        $responseBodyRawClipboard = $responseBodyContent;
    }
    // Not currently using - some APIs return plain text in the body
    //elseif ($responseBodyValid === false) {
    //    // Set error message
    //    $responseErrorShort = "JSON is not valid";
    //}
}
?>
<style>
    pre.sf-dump { width: 100%; }
</style>

<div id="pageSection" class="fixed top-0 flex h-screen w-screen flex-row pt-[41px] pb-[41px]">

    <div id="leftSection" class="layout-container flex flex-col" style="width: <?= $settings_home_leftSection ?>;">
        <div id="left-header" class="ml-5 mr-3 mt-5 mb-5">
            <div id="tab1-header" class="tab-header<?= $left_selectedTab_tab1 ?>">
                <div class="flex flex-row justify-between items-start">
                    <div class="font-bold">Collections <?php if (count($collectionsData) > 0) echo '(' . count($collectionsData) . ')' ?></div>
                    <?php if (UserModel::isLoggedIn()): ?>
                        <div class="flex flex-row items-center">
                            <?php if ($left_collectionId): ?>
                                <div class="px-2 mr-2 rounded-lg text-white bg-green-700 dark:bg-lime-700"><?= $left_collectionId ?></div>
                                <a href="/?unselect=collection"><button type="button" name="requestUnselect" class="general">Unselect</button></a>
                            <?php else: ?>
                                <button type="button" name="requestUnselect" class="general" disabled>Unselect</button>
                            <?php endif; ?>
                            <button type="button" name="collectionCreateOpenButton" data-modal="collectionCreateModal" data-input_collection-name="" data-focus="collectionName" class="general ml-2"><span><i class="fa-solid fa-plus"></i> Create</span></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="tab2-header" class="tab-header<?= $left_selectedTab_tab2 ?>">
                <div class="flex flex-row justify-between items-start">
                    <div class="font-bold">Requests <?php if (count($requestsData) > 0) echo '(' . count($requestsData) . ')' ?></div>
                    <?php if (UserModel::isLoggedIn()): ?>
                        <div class="flex flex-row items-center">
                            <?php if ($left_requestId): ?>
                                <div class="px-2 mr-2 rounded-lg text-white bg-green-700 dark:bg-lime-700"><?= $left_requestId ?></div>
                                <a href="/?unselect=request"><button type="button" name="requestUnselect" class="general">Unselect</button></a>
                            <?php else: ?>
                                <button type="button" name="requestUnselect" class="general" disabled>Unselect</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="tab3-header" class="tab-header<?= $left_selectedTab_tab3 ?>">
                <div class="font-bold">Variables</div>
            </div>
        </div>

        <div class="overflow-hidden flex flex-col h-full ml-5 mr-3">
            <div id="left-content" class="overflow-y-auto flex flex-col h-full">

                <div id="tab1-content" class="tab-content<?= $left_selectedTab_tab1 ?>">
                <?php if (UserModel::isLoggedIn()): ?>
                    <?php if (count($collectionsData) > 0): ?>
                        <table id="collectionsList">
                        <?php foreach ($collectionsData as $collectionModel): ?>
                            <tr id="<?= $collectionModel->getProperty('id') ?>" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 cursor-pointer<?php if ($collectionModel->getProperty('id') == $left_collectionId) { echo ' bg-zinc-100 dark:bg-zinc-800'; } ?>">
                                <td class="pl-2 pr-1 py-1 text-xs text-zinc-500 dark:text-zinc-400 text-right"><?= $collectionModel->getProperty('id') ?></td>
                                <td class="w-full px-1 py-1"><?= $collectionModel->getProperty('collectionName') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <div>No collections have been created yet.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div>Login to manage collections.</div>
                <?php endif; ?>
                </div>

                <div id="tab2-content" class="tab-content<?= $left_selectedTab_tab2 ?>">
                <?php if (UserModel::isLoggedIn()): ?>
                    <?php if (count($requestsData) > 0): ?>
                        <table id="requestsList">
                            <?php foreach ($requestsData as $requestModel): ?>
                                <tr id="<?= $requestModel->getProperty('id') ?>" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 cursor-pointer<?php if ($requestModel->getProperty('id') == $left_requestId) { echo ' bg-zinc-100 dark:bg-zinc-800'; } ?>">
                                    <td class="pl-2 pr-1 py-1 text-xs text-zinc-500 dark:text-zinc-400 text-right"><?= $requestModel->getProperty('id') ?></td>
                                    <td class="pl-1 pr-1 py-1 text-xs font-semibold dropdown-method-<?= $requestModel->getProperty('requestMethod') ?>"><?= $requestModel->requestMethodDisplay() ?></td>
                                    <td class="w-full px-1 py-1"><?= $requestModel->getProperty('requestName') ?></td>
                                    <?php if ($requestModel->getProperty('id') == $left_requestId): ?>
                                        <td class="px-2 py-1 text-right text-[75%] text-red-600 dark:text-red-700"><i id="requestModified" class="fa-solid fa-circle<?php if (!Application::app()->session()->get('home/upper/requestModified')) echo ' hidden' ?>"></i></td>
                                    <?php else: ?>
                                        <td></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php elseif ($left_collectionId): ?>
                        <div>No requests have been created yet for this collection.</div>
                        <?php if ($left_requestId): ?>
                            <div class="mt-3">Selected request ID <?= $left_requestId ?> belongs to collection:<br><?= RequestModel::collectionName($left_requestId) ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div>Select a collection to manage requests.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <div>Login to manage requests.</div>
                <?php endif; ?>
                </div>

                <div id="tab3-content" class="tab-content<?= $left_selectedTab_tab3 ?>">
                    <?php if (UserModel::isLoggedIn()): ?>
                        <?php if (count($variablesData) > 0): ?>
                            <table id="variablesList" class="variables">
                                <?php foreach ($variablesData as $VariableName => $variableData): ?>
                                    <?php
                                    $variableType = $variableData['type'];
                                    $variableValue = $variableData['value'];
                                    $variableTypeShort = strtoupper(substr($variableType, 0, 1));
                                    if (strlen($variableValue) > 50) {
                                        $variableValueShort = substr($variableValue, 0, 50) . '...';
                                    } else {
                                        $variableValueShort = $variableValue;
                                    }
                                    ?>
                                    <tr class="hover:bg-zinc-200 dark:hover:bg-zinc-700 cursor-default">
                                        <td class="w-full pl-2 pr-2 py-1 break-all">
                                            <span class="text-sm text-zinc-500 dark:text-zinc-400"><?= $VariableName . ' (' . $variableTypeShort . ')' ?></span>
                                            <br>
                                            <?= $variableValueShort ?>
                                        </td>
                                        <td class="pr-1">
                                            <button type="button" name="variableViewOpenButton" data-modal="variableViewModal" data-div_variable-name="<?= $VariableName ?>" data-textarea_variable-value="<?= $variableValue ?>" class="invisible px-1 cursor-pointer"><i class="fa-solid fa-magnifying-glass"></i></button><br>
                                            <?php if ($variableType == 'request'): ?>
                                                <button type="button" name="variableClearButton" value="<?= $VariableName ?>" class="invisible px-1 cursor-pointer"><i class="fa-solid fa-arrow-rotate-left"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php elseif (!$left_collectionId): ?>
                            <div>Select a collection to view variables.</div>
                        <?php else: ?>
                            <div>No variables have been created yet.</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div>Login to manage variables.</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="-mr-2 border-t border-b border-zinc-300 dark:border-zinc-650">
            <div class="m-5">
                <div>
                    <div class="flex flex-row justify-between items-center">
                    <?php if ($left_collectionId): ?>
                        <div><?= $left_collectionName ?></div>
                        <div class="flex flex-row">
                            <div class="mr-2">
                                <button type="button" name="collectionUpdateOpenButton" data-modal="collectionUpdateModal" data-input_collection-name="<?= $left_collectionName ?>" data-focus="collectionName" class="general w-8" style="padding-top: 7px;"><i class="fa-solid fa-pen"></i></button>
                            </div>
                            <div>
                                <button type="button" name="collectionDeleteOpenButton" data-modal="collectionDeleteModal" class="general w-8" style="padding-top: 7px;"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div>No collection selected</div>
                        <div class="flex flex-row">
                            <div class="mr-2">
                                <button type="button" class="general w-8" style="padding-top: 7px;" disabled><i class="fa-solid fa-pen"></i></button>
                            </div>
                            <div>
                                <button type="button" class="general w-8" style="padding-top: 7px;" disabled><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ml-4 mr-3 mt-4 mb-4">
            <div>
                <ul id="left" class="tabs">
                    <li id="tab1" class="w-[84px] tab-link<?= $left_selectedTab_tab1 ?>"><div class="flex flex-col items-center"><div><?= Output::icon('collections') ?></div><div>Collections</div></div></li>
                    <li id="tab2" class="w-[84px] tab-link<?= $left_selectedTab_tab2 ?>"><div class="flex flex-col items-center"><div><?= Output::icon('requests') ?></div><div>Requests</div></div></li>
                    <li id="tab3" class="w-[84px] tab-link<?= $left_selectedTab_tab3 ?>"><div class="flex flex-col items-center"><div><?= Output::icon('variables') ?></div><div>Variables</div></div></li>
                </ul>
            </div>
        </div>

    </div>

    <div id="borderVertical" class="borderVertical borderVerticalHover">
        <div id="borderVerticalLeft" class="borderVerticalLeft"></div>
        <div id="borderVerticalRight" class="borderVerticalRight"></div>
    </div>

    <div id="rightSection" class="flex flex-col" style="width: <?= $settings_home_rightSection ?>;">

        <div id="topSection" class="layout-container flex flex-col" style="height: <?= $settings_home_topSection ?>;">
            <form id="requestManage" action="/" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">

                <div class="ml-3 mr-5 mt-5 mb-3">

                    <div class="flex">
                        <div class="flex w-[60%] mr-4">
                            <div id="dropdownMethodButton" class="dropdown-method">
                                <div class="dropdown-method-button">
                                    <div class="dropdown-method-option-tick dropdown-method-option-tickoff"><i class="fa-solid fa-check"></i></div><div id="dropdownMethodValue" class="dropdown-method-value <?= "dropdown-method-$requestMethod" ?>"><?= $httpMethodList[$requestMethod] ?></div><i class="fa fa-chevron-left mr-2"></i>
                                </div>
                                <ul id="dropdownMethodList" data-type="method" class="dropdown-method-list">
                                <?php
                                foreach ($httpMethodList as $optionKey => $optionValue) {
                                    echo "<li id='$optionKey' data-label='$optionValue' class='dropdown-method-$optionKey'><div id='$optionKey-tick' class='dropdown-method-option-tick dropdown-method-option-tickoff'><i class='fa-solid fa-check'></i></div>$optionValue</li>\n";
                                }
                                ?>
                                </ul>
                            </div>

                            <div class="grow">
                                <input type="text" id="requestUrl" name="requestUrl" data-action="send" placeholder="Enter URL" spellcheck="false" oninvalid="this.setCustomValidity('Enter the URL')" oninput="this.setCustomValidity('')" value="<?= $requestUrl ?>" class="w-full h-[43px] p-2 font-mono border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650 focus:ring-0 focus:border-zinc-300 dark:focus:border-zinc-650 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"/>
                            </div>

                            <div>
                                <button type="button" id="sendSubmitButton" value="send" class="w-[90px] h-[43px] text-[14px] px-6 py-2 rounded-tr-lg rounded-br-lg font-bold text-white border bg-red-600 dark:bg-red-700 hover:bg-red-700 dark:hover:bg-red-600 border-red-800 dark:border-red-900">Send</button>
                            </div>
                        </div>

                        <div class="flex w-[40%]">
                            <div class="grow">
                                <input type="text" id="requestName" name="requestName" data-action="save" placeholder="Enter name" autocomplete="one-time-code" spellcheck="false" value="<?= $requestName ?>" class="w-full h-[43px] p-2 rounded-tl-lg rounded-bl-lg border border-r-0 border-zinc-300 dark:border-zinc-650 focus:ring-0 focus:border-zinc-300 dark:focus:border-zinc-650 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"/>
                            </div>

                            <div>
                                <button type="button" id="saveSubmitButton" value="save" class="h-[43px] text-[20px] px-4 pb-[2px] border bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600"><i class="fa-solid fa-floppy-disk"></i></button>
                            </div>

                            <div>
                                <?php if ($left_requestId): ?>
                                    <button type="button" id="cloneSubmitButton" value="clone" class="h-[43px] text-[20px] px-4 pb-[2px] border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600"><i class="fa-solid fa-clone"></i></button>
                                <?php else: ?>
                                    <button type="button" class="h-[43px] text-[20px] px-4 pb-[2px] text-zinc-400 dark:text-zinc-500 border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600 cursor-not-allowed" disabled><i class="fa-solid fa-clone"></i></button>
                                <?php endif; ?>
                            </div>

                            <div>
                            <?php if ($left_requestId): ?>
                                <button type="button" id="deleteSubmitButton" value="delete" class="h-[43px] text-[20px] px-4 pb-[2px] rounded-tr-lg rounded-br-lg border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600"><i class="fa-solid fa-trash-can"></i></button>
                            <?php else: ?>
                                <button type="button" class="h-[43px] text-[20px] px-4 pb-[2px] text-zinc-400 dark:text-zinc-500 rounded-tr-lg rounded-br-lg border border-l-0 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-900 border-zinc-300 dark:border-zinc-600 cursor-not-allowed" disabled><i class="fa-solid fa-trash-can"></i></button>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="modelClassName" value="RequestModel">
                    <input type="hidden" name="id" value="<?= $left_requestId ?>">
                    <input type="hidden" name="collectionId" value="<?= $left_collectionId ?>">
                    <input type="hidden" name="formAction" value="">
                    <input type="hidden" name="requestMethod" value="<?= $requestMethod ?>">
                    <input type="hidden" name="requestAuth" value="<?= $requestAuth ?>">
                    <input type="hidden" name="requestBody" value="<?= $requestBody ?>">
                    <input type="hidden" name="requestBodyTextType" value="<?= $requestBodyTextType ?>">
                    <input type="hidden" name="left_selectedTab" value="<?= $left_selectedTab ?>">
                    <input type="hidden" name="upper_selectedTab" value="<?= $upper_selectedTab ?>">
                    <input type="hidden" name="lower_selectedTab" value="<?= $lower_selectedTab ?>">
                    <input type="hidden" name="modalOpenName" value="<?= $modalOpenName ?>">

                    <div class="mt-5">
                        <div class="flex flex-row items-center justify-between h-[39px]">
                            <div>
                                <ul id="upper" class="tabs">
                                    <li id="tab1" class="tab-link<?= $upper_selectedTab_tab1 ?>">Params</li>
                                    <li id="tab2" class="tab-link<?= $upper_selectedTab_tab2 ?>">Headers</li>
                                    <li id="tab3" class="tab-link<?= $upper_selectedTab_tab3 ?>">Auth</li>
                                    <li id="tab4" class="tab-link<?= $upper_selectedTab_tab4 ?>">Body</li>
                                    <li id="tab5" class="tab-link<?= $upper_selectedTab_tab5 ?>">Variables</li>
                                    <li id="tab6" class="tab-link<?= $upper_selectedTab_tab6 ?>">Tests</li>
                                    <li id="tab7" class="tab-link<?= $upper_selectedTab_tab7 ?>">Settings</li>
                                </ul>
                            </div>
                            <div class="flex items-center">
                            <?php if ($requestError): ?>
                                <div id="requestUrlError" class="font-bold text-red-600 dark:text-red-700"><i class="fa-solid fa-circle-exclamation"></i> <?= $requestError ?></div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="overflow-hidden flex flex-col h-full ml-3 mr-5 mt-0 mb-3">
                    <div id="upper-content" class="overflow-y-auto flex flex-col h-full">

                        <div id="tab1-content" class="tab-content<?= $upper_selectedTab_tab1 ?>">
                            <div>
                                <table id="requestParamsInputs" class="table-auto text-left text-sm">
                                    <thead>
                                    <tr class="h-8">
                                        <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Name</th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</th>
                                        <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($requestParamsInputs as $requestParamsInput): ?>
                                        <?php
                                        $requestParamName = $requestParamsInput['name'];
                                        $requestParamValue = $requestParamsInput['value'];
                                        $requestParamEnabled = $requestParamsInput['enabled'];
                                        $requestParamEnabled == 'on' ? $requestParamEnabledCheckbox = ' checked' : $requestParamEnabledCheckbox = null;
                                        $requestParamName = htmlspecialchars($requestParamName);
                                        $requestParamValue = htmlspecialchars($requestParamValue);
                                        ?>
                                        <!--Existing row-->
                                        <tr class="h-8">
                                            <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                <input type="checkbox" name="requestParamCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $requestParamEnabledCheckbox ?>>
                                                <input type="hidden" name="requestParamEnabled[]" value="<?= $requestParamEnabled ?>">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestParamName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" value="<?= $requestParamName ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestParamValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" value="<?= $requestParamValue ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
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
                                    <!--New row-->
                                    <tr class="h-8 nodrop">
                                        <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                            <input type="checkbox" name="requestParamCheckbox[]" class="bg-transparent dark:bg-transparent" disabled>
                                            <input type="hidden" name="requestParamEnabled[]">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="requestParamName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="requestParamValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                            <button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                        </td>
                                        <td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td>
                                        <td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="tab2-content" class="tab-content<?= $upper_selectedTab_tab2 ?>">
                            <div>
                                <table id="requestHeadersInputs" class="table-auto text-left text-sm">
                                    <thead>
                                    <tr class="h-8">
                                        <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Name</th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</th>
                                        <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($requestHeadersInputs as $requestHeadersInput): ?>
                                        <?php
                                        $requestHeaderName = $requestHeadersInput['name'];
                                        $requestHeaderValue = $requestHeadersInput['value'];
                                        $requestHeaderEnabled = $requestHeadersInput['enabled'];
                                        $requestHeaderEnabled == 'on' ? $requestHeaderEnabledCheckbox = ' checked' : $requestHeaderEnabledCheckbox = null;
                                        $requestHeaderName = htmlspecialchars($requestHeaderName);
                                        $requestHeaderValue = htmlspecialchars($requestHeaderValue);
                                        ?>
                                        <!--Existing row-->
                                        <tr class="h-8">
                                            <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                <input type="checkbox" name="requestHeaderCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $requestHeaderEnabledCheckbox ?>>
                                                <input type="hidden" name="requestHeaderEnabled[]" value="<?= $requestHeaderEnabled ?>">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestHeaderName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" value="<?= $requestHeaderName ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestHeaderValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" value="<?= $requestHeaderValue ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
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
                                    <!--New row-->
                                    <tr class="h-8 nodrop">
                                        <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                            <input type="checkbox" name="requestHeaderCheckbox[]" class="bg-transparent dark:bg-transparent" disabled>
                                            <input type="hidden" name="requestHeaderEnabled[]">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="requestHeaderName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="requestHeaderValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                            <button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                        </td>
                                        <td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td>
                                        <td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="tab3-content" class="h-full tab-content<?= $upper_selectedTab_tab3 ?>">

                            <div id="tab3-subcontent-none" class="tab-subcontent<?= $upper_selectedTab_tab3_none ?>">
                                No authentication.
                            </div>

                            <div id="tab3-subcontent-basic" class="tab-subcontent<?= $upper_selectedTab_tab3_basic ?>">
                                <div>
                                    <table id="requestAuthBasic" class="table-auto text-left text-sm">
                                        <tr class="h-8">
                                            <td class="min-w-24 px-2 py-0 font-semibold text-right border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Username</td>
                                            <td colspan="2" class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" id="requestAuthBasicUsername" name="requestAuthBasicUsername" autocomplete="one-time-code" placeholder="Username" spellcheck="false" value="<?= $requestAuthBasicUsername ?>" class="min-w-[350px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                        </tr>
                                        <tr class="h-8">
                                            <td class="min-w-24 px-2 py-0 font-semibold text-right border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Password</td>
                                            <td class="px-1 py-1 border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <input type="password" id="requestAuthBasicPassword" name="requestAuthBasicPassword" autocomplete="one-time-code" placeholder="Password" spellcheck="false" value="<?= $requestAuthBasicPassword ?>" class="min-w-[350px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td id="requestAuthBasicPasswordShow" class="px-2 cursor-pointer border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-eye"></i></td>
                                            <td id="requestAuthBasicPasswordHide" class="hidden px-2 cursor-pointer border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-regular fa-eye"></i></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div id="tab3-subcontent-token" class="tab-subcontent<?= $upper_selectedTab_tab3_token ?>">
                                <div>
                                    <table id="requestAuthToken" class="table-auto text-left text-sm">
                                        <tr class="h-8">
                                            <td class="min-w-24 px-2 py-0 font-semibold text-right border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Token</td>
                                            <td class="px-1 py-1 border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <input type="password" id="requestAuthTokenValue" name="requestAuthTokenValue" autocomplete="one-time-code" placeholder="Token" spellcheck="false" value="<?= $requestAuthTokenValue ?>" class="min-w-[450px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td id="requestAuthTokenValueShow" class="px-2 cursor-pointer border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-eye"></i></td>
                                            <td id="requestAuthTokenValueHide" class="hidden px-2 cursor-pointer border border-l-0 border-zinc-300 dark:border-zinc-650"><i class="fa-regular fa-eye"></i></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div id="tab3-subcontent-header" class="tab-subcontent<?= $upper_selectedTab_tab3_header ?>">
                                <div>
                                    <table id="requestAuthHeader" class="h-fit table-auto text-left text-sm">
                                        <tr class="h-8">
                                            <td class="min-w-24 px-2 py-0 font-semibold text-right border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Name</td>
                                            <td colspan="2" class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" id="requestAuthHeaderName" name="requestAuthHeaderName" autocomplete="one-time-code" placeholder="Name" spellcheck="false" value="<?= $requestAuthHeaderName ?>" class="min-w-[350px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                        </tr>
                                        <tr class="h-8">
                                            <td class="px-2 py-0 font-semibold text-right border border-b-0 border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</td>
                                            <td rowspan="5" class="content-start px-1 py-1 border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <div class="table h-full">
                                                    <textarea rows="6" id="requestAuthHeaderValue" name="requestAuthHeaderValue" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="textarea-password min-w-[350px] h-full px-1 py-0 text-sm font-mono resize-none border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"><?= $requestAuthHeaderValue ?></textarea>
                                                </div>
                                            </td>
                                            <td id="requestAuthHeaderValueShow" class="px-2 cursor-pointer border border-l-0 border-b-0 border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-eye"></i></td>
                                            <td id="requestAuthHeaderValueHide" class="hidden px-2 cursor-pointer border border-l-0 border-b-0 border-zinc-300 dark:border-zinc-650"><i class="fa-regular fa-eye"></i></td>
                                        </tr>
                                        <tr class="h-8">
                                            <td rowspan="4" class="border border-t-0 border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></td>
                                            <td rowspan="4" class="border border-l-0 border-t-0 border-zinc-300 dark:border-zinc-650"></td>
                                        </tr>
                                        <tr class="h-8"></tr>
                                        <tr class="h-8"></tr>
                                        <tr class="h-8"></tr>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <div id="tab4-content" class="h-full tab-content<?= $upper_selectedTab_tab4 ?>">
                            <div id="tab4-subcontent-none" class="tab-subcontent<?= $upper_selectedTab_tab4_none ?>">
                                No body.
                            </div>
                            <div id="tab4-subcontent-text" class="h-full tab-subcontent<?= $upper_selectedTab_tab4_text ?>">
                                <textarea id="requestBodyTextValue" name="requestBodyTextValue" placeholder="Enter text" spellcheck="false" oninvalid="this.setCustomValidity('Enter the text')" oninput="this.setCustomValidity('')" class="h-full px-2 py-0 resize-none text-base font-mono leading-[28.5px] border border-zinc-300 dark:border-zinc-600 focus:ring-0 focus:border-zinc-300 dark:focus:border-zinc-600 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none"><?= $requestBodyTextValue ?></textarea>
                            </div>
                            <div id="tab4-subcontent-form" class="tab-subcontent<?= $upper_selectedTab_tab4_form ?>">
                                <div>
                                    <table id="requestBodyFormInputs" class="table-auto text-left text-sm">
                                        <thead>
                                        <tr class="h-8">
                                            <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                            <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Name</th>
                                            <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</th>
                                            <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($requestBodyFormInputs as $requestBodyFormInput): ?>
                                            <?php
                                            $requestBodyFormInputName = $requestBodyFormInput['name'];
                                            $requestBodyFormInputValue = $requestBodyFormInput['value'];
                                            $requestBodyFormInputEnabled = $requestBodyFormInput['enabled'];
                                            $requestBodyFormInputEnabled == 'on' ? $requestBodyFormInputEnabledCheckbox = ' checked' : $requestBodyFormInputEnabledCheckbox = null;
                                            $requestBodyFormInputName = htmlspecialchars($requestBodyFormInputName);
                                            $requestBodyFormInputValue = htmlspecialchars($requestBodyFormInputValue);
                                            ?>
                                            <!--Existing row-->
                                            <tr class="h-8">
                                                <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                    <input type="checkbox" name="requestBodyFormInputCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $requestBodyFormInputEnabledCheckbox ?>>
                                                    <input type="hidden" name="requestBodyFormInputEnabled[]" value="<?= $requestBodyFormInputEnabled ?>">
                                                </td>
                                                <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                    <input type="text" name="requestBodyFormInputName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" value="<?= $requestBodyFormInputName ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                                </td>
                                                <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                    <input type="text" name="requestBodyFormInputValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" value="<?= $requestBodyFormInputValue ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
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
                                        <!--New row-->
                                        <tr class="h-8 nodrop">
                                            <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                <input type="checkbox" name="requestBodyFormInputCheckbox[]" class="bg-transparent dark:bg-transparent" disabled>
                                                <input type="hidden" name="requestBodyFormInputEnabled[]">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestBodyFormInputName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestBodyFormInputValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                            </td>
                                            <td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td>
                                            <td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                                <i class="fa-solid fa-grip-vertical"></i>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div id="tab4-subcontent-file" class="tab-subcontent<?= $upper_selectedTab_tab4_file ?>">
                            <?php if ($uploadedBodyFiles): ?>
                                <div>
                                    <table id="bodyFiles" class="table-auto mb-5 text-left text-sm">
                                        <thead>
                                        <tr class="h-8">
                                            <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                            <th class="px-2 py-0 min-w-72 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">File</th>
                                            <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($uploadedBodyFiles as $uploadedBodyFile): ?>
                                            <?php
                                            // Set file path
                                            $uploadedBodyFilePath = $uploadedBodyFilesDirectory . '/' . $uploadedBodyFile;

                                            // Determine which file is checked
                                            $uploadedBodyFilePath == $requestBodyFileExisting ? $requestBodyFileExistingCheckbox = ' checked' : $requestBodyFileExistingCheckbox = null;
                                            ?>
                                            <tr class="h-8">
                                                <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                    <input type="checkbox" name="requestBodyFileExisting" value="<?= $uploadedBodyFilePath ?>" class="bg-transparent dark:bg-transparent"<?= $requestBodyFileExistingCheckbox ?>>
                                                </td>
                                                <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                    <?= $uploadedBodyFile ?>
                                                </td>
                                                <td class="w-8 py-1 text-center border border-zinc-300 dark:border-zinc-650">
                                                    <button type="button" name="deleteButton" class="px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                                <div class="flex flex-row">
                                    <input type="file" id="requestBodyFile" name="requestBodyFile" class="input-file">
                                    <label for="requestBodyFile"><i class="fa-solid fa-upload mr-2"></i>Upload file</label>
                                    <div id="requestBodyFileValue" class="flex ml-3 items-center"></div>
                                    <div id="requestBodyFileClear" class="hidden flex px-2 items-center cursor-pointer"><i class="fa-solid fa-xmark"></i></div>
                                </div>
                            </div>

                        </div>

                        <div id="tab5-content" class="tab-content<?= $upper_selectedTab_tab5 ?>">
                            <div>
                                <table id="requestVariablesInputs" class="table-auto text-left text-sm">
                                    <thead>
                                    <tr class="h-8">
                                        <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">JSON key</th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Variable name</th>
                                        <th class="px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</th>
                                        <th colspan="4" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($requestVariablesInputs as $requestVariablesInput): ?>
                                        <?php
                                        $requestVariableKey = $requestVariablesInput['key'];
                                        $requestVariableName = $requestVariablesInput['name'];
                                        $requestVariableEnabled = $requestVariablesInput['enabled'];
                                        $requestVariableEnabled == 'on' ? $requestVariableEnabledCheckbox = ' checked' : $requestVariableEnabledCheckbox = null;
                                        $requestVariableValue = Application::app()->session()->get("variables/$left_requestCollectionId/$requestVariableName")['value'] ?? '';
                                        $requestVariableKey = htmlspecialchars($requestVariableKey);
                                        $requestVariableName = htmlspecialchars($requestVariableName);
                                        $requestVariableValue = htmlspecialchars($requestVariableValue);

                                        if (strlen($requestVariableValue) > 50) {
                                            $requestVariableValueShort = substr($requestVariableValue, 0, 50) . '...';
                                        } else {
                                            $requestVariableValueShort = $requestVariableValue;
                                        }
                                        ?>
                                        <!--Existing row-->
                                        <tr class="h-8">
                                            <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                <input type="checkbox" name="requestVariableCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $requestVariableEnabledCheckbox ?>>
                                                <input type="hidden" name="requestVariableEnabled[]" value="<?= $requestVariableEnabled ?>">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestVariableKey[]" autocomplete="one-time-code" placeholder="JSON key" spellcheck="false" value="<?= $requestVariableKey ?>" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="requestVariableName[]" autocomplete="one-time-code" placeholder="Variable name" spellcheck="false" value="<?= $requestVariableName ?>" class="w-[300px] px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="px-2 py-1 font-mono break-all border border-zinc-300 dark:border-zinc-650">
                                                <?= $requestVariableValueShort ?>
                                            </td>
                                            <?php if ($requestVariableValue): ?>
                                                <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                    <button type="button" name="variableViewOpenButton" data-modal="variableViewModal" data-div_variable-name="<?= $requestVariableName ?>" data-textarea_variable-value="<?= $requestVariableValue ?>" class="px-1 text-base"><i class="fa-solid fa-magnifying-glass"></i></button>
                                                </td>
                                            <?php else: ?>
                                                <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                    <button type="button" name="showButton" class="px-1 text-base" disabled><i class="fa-solid fa-magnifying-glass text-zinc-300 dark:text-zinc-500"></i></button>
                                                </td>
                                            <?php endif; ?>
                                            <?php if ($requestVariableValue): ?>
                                                <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                                    <button type="button" name="clearButton" data-variable="<?= $requestVariableName ?>" data-collection="<?= $left_requestCollectionId ?>" class="px-1 text-base"><i class="fa-solid fa-arrow-rotate-left"></i></button>
                                                </td>
                                            <?php else: ?>
                                                <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                                    <button type="button" name="clearButton" class="px-1 text-base" disabled><i class="fa-solid fa-arrow-rotate-left text-zinc-300 dark:text-zinc-500"></i></button>
                                                </td>
                                            <?php endif; ?>
                                            <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <button type="button" name="deleteButton" class="px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                            </td>
                                            <td class="dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                                <div id="dragHandle">
                                                    <i class="fa-solid fa-grip-vertical"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!--New row-->
                                    <tr class="h-8 nodrop">
                                        <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                            <input type="checkbox" name="requestVariableCheckbox[]" class="bg-transparent dark:bg-transparent" disabled>
                                            <input type="hidden" name="requestVariableEnabled[]">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="requestVariableKey[]" autocomplete="one-time-code" placeholder="JSON key" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="requestVariableName[]" autocomplete="one-time-code" placeholder="Variable name" spellcheck="false" class="px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="px-2 py-1 font-mono border border-zinc-300 dark:border-zinc-650"></td>
                                        <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                            <button type="button" name="showButton" class="hidden px-1 text-base" disabled><i class="fa-solid fa-magnifying-glass text-zinc-300 dark:text-zinc-500"></i></button>
                                        </td>
                                        <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                            <button type="button" name="clearButton" class="hidden px-1 text-base" disabled><i class="fa-solid fa-arrow-rotate-left text-zinc-300 dark:text-zinc-500"></i></button>
                                        </td>
                                        <td class="w-8 py-1 text-center border border-l-0 border-r-0 border-zinc-300 dark:border-zinc-650">
                                            <button type="button" name="deleteButton" class="hidden px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                        </td>
                                        <td id="tempCell" class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650"></td>
                                        <td id="dragCell" class="hidden dragHandle cursor-move w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                            <i class="fa-solid fa-grip-vertical"></i>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="tab6-content" class="tab-content<?= $upper_selectedTab_tab6 ?>">
                            <div>
                                <?php if (count($testsData) > 0): ?>
                                    <table class="table-auto text-left text-sm">
                                        <tr>
                                            <th class="table-heading">Name</th>
                                            <th class="table-heading">Type</th>
                                            <th class="table-heading">Assertion</th>
                                            <th colspan="2" class="table-heading"></th>
                                        </tr>
                                    <?php foreach ($testsData as $testModel): ?>
                                        <?php
                                        $testId = $testModel->getProperty('id');
                                        $testName = $testModel->getProperty('testName');
                                        $testType = $testModel->getProperty('testType');
                                        $testAssertion = $testModel->getProperty('testAssertion');
                                        ?>
                                        <tr class="h-8">
                                            <td class="min-w-40 table-cell"><?= $testName ?></td>
                                            <td class="min-w-40 table-cell"><?= $testType ?></td>
                                            <td class="min-w-40 table-cell"><?= $testAssertion ?></td>

                                            <td class="w-8 py-1 text-center border border-r-0 border-zinc-300 dark:border-zinc-650">
                                                <button type="button" name="testUpdateOpenButton" data-modal="testUpdateModal" data-input_id="<?= $testId ?>" data-input_test-name="<?= $testName ?>" data-input_test-type="<?= $testType ?>" data-input_test-assertion="<?= $testAssertion ?>" data-focus="testName" class="px-1 text-base"><i class="fa-solid fa-pen"></i></button>
                                            </td>
                                            <td class="w-8 py-1 text-center border border-l-0 border-zinc-300 dark:border-zinc-650">
                                                <button type="button" name="testDeleteOpenButton" data-modal="testDeleteModal" data-input_id="<?= $testId ?>" data-input_test-name="<?= $testName ?>" class="px-1 text-base"><i class="fa-solid fa-trash-can"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </table>
                                <?php elseif ($left_requestId): ?>
                                    <div>No unit tests have been created yet for this request.</div>
                                <?php else: ?>
                                    <div>Save this request to manage tests.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div id="tab7-content" class="tab-content<?= $upper_selectedTab_tab7 ?>">
                            <div class="mr-5">
                                <table class="text-left border-collapse text-sm">
                                    <tr>
                                        <th class="table-heading">Setting</th>
                                        <th class="table-heading">Value</th>
                                    </tr>
                                    <tr>
                                        <td class="table-cell">Default scheme</td>
                                        <td class="table-cell"><?= $display_http_defaultScheme ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-cell">Timeout</td>
                                        <td class="table-cell"><?= $display_http_timeout ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-cell">HTTP version</td>
                                        <td class="table-cell"><?= $display_http_version ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-cell">Accept header</td>
                                        <td class="table-cell"><?= $display_http_accept ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="upper-footer">

                    <div id="tab3-footer" class="ml-3 mr-5 mt-2 mb-3 tab-footer<?= $upper_selectedTab_tab3 ?>">
                        <div class="flex">
                            <div id="dropdownAuthButton" data-tab="tab3" class="dropdown-general">
                                <div class="dropdown-general-button">
                                    <div class="dropdown-general-option-tick dropdown-general-option-tickoff"><i class="fa-solid fa-check"></i></div><div id="dropdownAuthValue" class="dropdown-general-value"><?= $httpAuthListIcons[$requestAuth] . $httpAuthList[$requestAuth] ?></div><i class="fa fa-chevron-left mr-2"></i>
                                </div>
                                <ul id="dropdownAuthList" data-type="auth" class="dropdown-general-list">
                                <?php
                                foreach ($httpAuthList as $optionKey => $optionValue) {
                                    echo "<li id='$optionKey' data-label='$optionValue' class='dropdown-general-$optionKey'><div id='$optionKey-tick' class='dropdown-general-option-tick dropdown-general-option-tickoff'><i class='fa-solid fa-check'></i></div><div id='$optionKey-text' class='dropdown-general-option-text'>" . $httpAuthListIcons[$optionKey] . "$optionValue</div></li>\n";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="tab4-footer" class="ml-3 mr-5 mt-2 mb-3 tab-footer<?= $upper_selectedTab_tab4 ?>">
                        <div class="flex">
                            <div id="dropdownBodyButton" data-tab="tab4" class="dropdown-general">
                                <div class="dropdown-general-button">
                                    <div class="dropdown-general-option-tick dropdown-general-option-tickoff"><i class="fa-solid fa-check"></i></div><div id="dropdownBodyValue" class="dropdown-general-value"><?= $httpBodyListIcons[$requestBody] . $httpBodyList[$requestBody] ?></div><i class="fa fa-chevron-left mr-2"></i>
                                </div>
                                <ul id="dropdownBodyList" data-type="body" class="dropdown-general-list">
                                <?php
                                foreach ($httpBodyList as $optionKey => $optionValue) {
                                    echo "<li id='$optionKey' data-label='$optionValue' class='dropdown-general-$optionKey'><div id='$optionKey-tick' class='dropdown-general-option-tick dropdown-general-option-tickoff'><i class='fa-solid fa-check'></i></div><div id='$optionKey-text' class='dropdown-general-option-text'>" . $httpBodyListIcons[$optionKey] . "$optionValue</div></li>\n";
                                }
                                ?>
                                </ul>
                            </div>
                            <div id="dropdownBodyTextTypeButton" data-tab="tab4" class="<?= $requestBodyClass ?>ml-5 dropdown-general">
                                <div class="dropdown-general-button">
                                    <div class="dropdown-general-option-tick dropdown-general-option-tickoff"><i class="fa-solid fa-check"></i></div><div id="dropdownBodyTextTypeValue" class="dropdown-general-value"><?= $httpBodyTextTypeList[$requestBodyTextType] ?></div><i class="fa fa-chevron-left mr-2"></i>
                                </div>
                                <ul id="dropdownBodyTextTypeList" data-type="bodyTextType" class="dropdown-general-list">
                                <?php
                                foreach ($httpBodyTextTypeList as $optionKey => $optionValue) {
                                    echo "<li id='$optionKey' data-label='$optionValue' class='dropdown-general-$optionKey'><div id='$optionKey-tick' class='dropdown-general-option-tick dropdown-general-option-tickoff'><i class='fa-solid fa-check'></i></div><div id='$optionKey-text' class='dropdown-general-option-text'>$optionValue</div></li>\n";
                                }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="tab6-footer" class="ml-3 mr-5 mt-2 mb-3 tab-footer<?= $upper_selectedTab_tab6 ?>">
                        <div>
                            <button type="button" name="testCreateOpenButton" data-modal="testCreateModal" data-input_test-name="" data-input_test-type="" data-input_test-assertion="" data-focus="testName" class="general"<?php if (!$left_requestId) echo ' disabled'; ?>><span><i class="fa-solid fa-plus"></i> Create</span></button>
                        </div>
                    </div>

                    <div id="tab7-footer" class="ml-3 mr-5 mt-2 mb-3 tab-footer<?= $upper_selectedTab_tab7 ?>">
                    <?php if (UserModel::isLoggedIn()): ?>
                        <div>
                            <a href="/?select=settings&tab=tab1" target="_blank"><button type="button" class="general"><span><i class="fa-solid fa-sliders mr-2"></i> Update settings</span></button></a>
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

        <div id="bottomSection" class="layout-container flex flex-col" style="height: <?= $settings_home_bottomSection ?>;">

            <div class="ml-3 mr-5 mt-3 mb-5">
                <div class="flex flex-row items-center justify-between h-[39px]">

                    <div>
                        <ul id="lower" class="tabs">
                            <li id="tab1" class="tab-link<?= $lower_selectedTab_tab1 ?>">Body</li>
                            <li id="tab2" class="tab-link<?= $lower_selectedTab_tab2 ?>">Headers<?php if ($responseHeaders) echo ' (' . count($responseHeaders) . ')'; ?></li>
                            <li id="tab3" class="tab-link<?= $lower_selectedTab_tab3 ?>">Raw</li>
                            <li id="tab4" class="tab-link<?= $lower_selectedTab_tab4 ?>">HTML</li>
                            <li id="tab5" class="tab-link<?= $lower_selectedTab_tab5 ?>">Tests</li>
                            <li id="tab6" class="tab-link<?= $lower_selectedTab_tab6 ?>">Settings</li>
                        </ul>
                    </div>

                    <div id="responseStatus" class="flex flex-row items-center">
                    <?php if ($responseStatusLine): ?>
                        <?php if ($responseValid && $responseErrorShort): ?>
                            <div class="pr-4 font-bold text-red-600 dark:text-red-700 border-r border-zinc-300 dark:border-zinc-650"><i class="fa-solid fa-circle-exclamation"></i> <?= $responseErrorShort ?></div>
                        <?php endif; ?>
                        <div class="pl-4 pr-2 font-bold text-zinc-400 dark:text-zinc-400"><?= $responseStatusProtocol ?></div>
                        <?php if ($responseException): ?>
                            <div class="pr-4 font-bold text-red-600 dark:text-red-700">
                                <?= "$responseStatusCode $responseStatusText" ?>
                            </div>
                        <?php else: ?>
                            <div class="pr-4 font-bold text-[#009900] dark:text-[#00b300]">
                                <?= "$responseStatusCode $responseStatusText" ?>
                            </div>
                        <?php endif; ?>

                        <div class="px-4 border-l border-zinc-300 dark:border-zinc-650"><?= $responseTimeFormatted ?></div>

                        <?php if ($responseBodySizeFormatted): ?>
                            <div class="px-4 border-l border-zinc-300 dark:border-zinc-650"><?= $responseBodySizeFormatted ?></div>
                        <?php endif; ?>

                        <div class="px-4 border-l border-zinc-300 dark:border-zinc-650"><?= $responseRequestAgeDisplay ?></div>

                        <div class="px-4 border-l border-r border-zinc-300 dark:border-zinc-650"><?= Output::icon($responseSchemeIcon) ?></div>
                        <div id="clipboard-button-enabled" class="hidden">
                            <div class="pl-2 pt-[3px] text-zinc-700 dark:text-zinc-300">
                                <button type="button" id="clipboard-button" class="p-0 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700">
                                    <div id="clipboard-icon" class="flex px-2 py-2">
                                        <?= Output::icon('clipboard') ?>
                                    </div>
                                    <div id="clipboard-copied" class="hidden flex px-2 py-2 text-green-700 dark:text-lime-600">
                                        <?= Output::icon('check') ?>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div id="clipboard-button-disabled">
                            <div title="<?= $clipboardTitle ?>" class="pl-2 pt-[3px] text-zinc-300 dark:text-zinc-500">
                                <div class="px-2 py-2">
                                    <?= Output::icon('clipboard') ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div id="responseStatusProgress" class="hidden flex flex-row items-center">
                        <div class="text-3xl"><i class="fa-solid fa-sync fa-spin"></i></div>
                    </div>

                </div>
            </div>

            <div class="overflow-hidden flex flex-col h-full ml-3 mr-5 mt-0 mb-5">
                <div id="lower-content" class="overflow-y-auto flex flex-col h-full">

                    <div id="tab1-content" class="tab-content<?= $lower_selectedTab_tab1 ?> overflow-y-scroll h-full border border-zinc-300 dark:border-zinc-650">
                    <?php if ($responseBodyEncoded): ?>
                        <div id="json-output" spellcheck="false" class="json-container w-full min-h-full text-wrap font-mono focus:outline-none select-text cursor-text"></div>
                    <?php elseif ($responseBodyContent): ?>
                        <div spellcheck="false" class="raw-container w-full min-h-full text-wrap focus:outline-none select-text cursor-text">
                            <ol class="raw-text">
                                <?php foreach ($responseBodySafeArray as $responseBodySafeLine): ?>
                                    <li><?= $responseBodySafeLine ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    <?php elseif ($responseErrorLong): ?>
                        <div class="w-full min-h-full p-4 font-bold text-red-600 dark:text-red-700">
                            <div class="mb-2 text-3xl"><i class="fa-solid fa-circle-exclamation"></i></div>
                            <div><?= $responseErrorLong ?></div>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div id="tab2-content" class="tab-content<?= $lower_selectedTab_tab2 ?>">
                    <?php if ($responseHeaders): ?>
                        <div class="mr-5 mb-5">
                            <table class="text-left border-collapse text-sm">
                                <tr>
                                    <th class="table-heading">Name</th>
                                    <th class="table-heading">Value</th>
                                </tr>
                                <?php
                                foreach ($responseHeaders as $responseHeaderName => $responseHeaderValue) {
                                    echo "<tr>\n";
                                    echo "  <td class='table-cell whitespace-nowrap content-start font-mono'>$responseHeaderName</th>\n";
                                    echo "  <td class='table-cell content-start font-mono break-all'>$responseHeaderValue</th>\n";
                                    echo "</tr>\n";
                                }
                                ?>
                            </table>
                        </div>
                    <?php elseif ($responseErrorLong): ?>
                        <div class="font-bold text-red-600 dark:text-red-700">
                            <?= $responseErrorLong ?>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div id="tab3-content" class="tab-content<?= $lower_selectedTab_tab3 ?> overflow-y-scroll h-full border border-zinc-300 dark:border-zinc-650">
                    <?php if ($responseBodyContent): ?>
                        <div spellcheck="false" class="raw-container w-full min-h-full text-wrap focus:outline-none select-text cursor-text">
                            <ol class="raw-text">
                                <?php foreach ($responseBodySafeArray as $responseBodySafeLine): ?>
                                    <li><?= $responseBodySafeLine ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    <?php elseif ($responseErrorLong): ?>
                        <div class="w-full min-h-full p-4 font-bold text-red-600 dark:text-red-700">
                            <div class="mb-2 text-3xl"><i class="fa-solid fa-circle-exclamation"></i></div>
                            <div><?= $responseErrorLong ?></div>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div id="tab4-content" class="tab-content<?= $lower_selectedTab_tab4 ?> overflow-y-hidden h-full border border-zinc-300 dark:border-zinc-650">
                        <?php if ($responseBodyContent): ?>
                            <iframe class="w-full h-full" src="/html"></iframe>
                        <?php elseif ($responseErrorLong): ?>
                            <div class="w-full min-h-full p-4 font-bold text-red-600 dark:text-red-700">
                                <div class="mb-2 text-3xl"><i class="fa-solid fa-circle-exclamation"></i></div>
                                <div><?= $responseErrorLong ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="tab5-content" class="tab-content<?= $lower_selectedTab_tab5 ?>">
                        <?php if ($responseValid): ?>
                            <?php if ($testsResults): ?>
                                <div class="mr-5 mb-5">
                                    <table class="table-auto text-left text-sm">
                                        <tr>
                                            <th class="table-heading">Name</th>
                                            <th class="table-heading">Type</th>
                                            <th class="table-heading">Assertion</th>
                                            <th class="table-heading">Value</th>
                                            <th class="table-heading">Result</th>
                                        </tr>
                                        <?php foreach ($testsResults as $testsResult): ?>
                                            <?php
                                            $testId = $testsResult['id'];
                                            $testName = $testsResult['name'];
                                            $testType = $testsResult['type'];
                                            $testAssertion = $testsResult['assertion'];
                                            $testValue = $testsResult['value'];
                                            $testResult = $testsResult['result'];

                                            if ($testResult == 'passed') {
                                                $testResultClass = 'bg-green-700 dark:bg-lime-700';
                                            } elseif ($testResult == 'failed') {
                                                $testResultClass = 'bg-red-600 dark:bg-red-700';
                                            } elseif ($testResult == 'skipped') {
                                                $testResultClass = 'bg-zinc-500 dark:bg-zinc-600';
                                            }
                                            ?>
                                            <tr class="h-8">
                                                <td class="min-w-40 table-cell"><?= $testName ?></td>
                                                <td class="min-w-40 table-cell"><?= $testType ?></td>
                                                <td class="min-w-40 table-cell"><?= $testAssertion ?></td>
                                                <td class="min-w-40 table-cell"><?= $testValue ?></td>
                                                <td class="min-w-20 text-white table-cell <?= $testResultClass ?>"><?= ucfirst($testResult) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div>No unit tests have run.</div>
                            <?php endif; ?>
                        <?php elseif ($responseErrorLong): ?>
                            <div class="font-bold text-red-600 dark:text-red-700">
                                <?= $responseErrorLong ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="tab6-content" class="tab-content<?= $lower_selectedTab_tab6 ?>">
                        <div class="mr-5 mb-5">
                            <table class="text-left border-collapse text-sm">
                                <tr>
                                    <th class="table-heading">Setting</th>
                                    <th class="table-heading">Value</th>
                                </tr>
                                <tr>
                                    <td class="table-cell">Sort headers</td>
                                    <td class="table-cell"><?= $display_http_sortHeaders ?></td>
                                </tr>
                                <tr>
                                    <td class="table-cell">Line numbers</td>
                                    <td class="table-cell"><?= $display_json_lineNumbers ?></td>
                                </tr>
                                <tr>
                                    <td class="table-cell">JSON indent</td>
                                    <td class="table-cell"><?= $display_json_indent ?></td>
                                </tr>
                                <tr>
                                    <td class="table-cell">JSON URL links</td>
                                    <td class="table-cell"><?= $display_json_linkUrls ?></td>
                                </tr>
                                <tr>
                                    <td class="table-cell">JSON trailing commas</td>
                                    <td class="table-cell"><?= $display_json_trailingCommas ?></td>
                                </tr>
                                <tr>
                                    <td class="table-cell">JSON quote keys</td>
                                    <td class="table-cell"><?= $display_json_quoteKeys ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <div id="lower-footer">

                <div id="tab6-footer" class="ml-3 mr-5 mt-0 mb-5 tab-footer<?= $lower_selectedTab_tab6 ?>">
                <?php if (UserModel::isLoggedIn()): ?>
                    <div>
                        <a href="/?select=settings&tab=tab2" target="_blank"><button type="button" class="general"><span><i class="fa-solid fa-sliders mr-2"></i> Update settings</span></button></a>
                    </div>
                <?php else: ?>
                    <div>Login to update settings.</div>
                <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

</div>

<textarea id="lower-tab-clipboard" class="hidden overflow-hidden w-[1px] h-[1px] p-0 border-0 resize-none" name="lower-tab-clipboard"></textarea>
<textarea id="lower-tab1-clipboard" class="hidden overflow-hidden w-0 h-0 p-0 border-0 resize-none" name="lower-tab1-clipboard"><?= $responseBodyClipboard ?></textarea>
<textarea id="lower-tab2-clipboard" class="hidden overflow-hidden w-0 h-0 p-0 border-0 resize-none" name="lower-tab2-clipboard"><?= $responseHeadersClipboard ?></textarea>
<textarea id="lower-tab3-clipboard" class="hidden overflow-hidden w-0 h-0 p-0 border-0 resize-none" name="lower-tab3-clipboard"><?= $responseBodyRawClipboard ?></textarea>
<textarea id="lower-tab4-clipboard" class="hidden overflow-hidden w-0 h-0 p-0 border-0 resize-none" name="lower-tab4-clipboard"></textarea>
<textarea id="lower-tab5-clipboard" class="hidden overflow-hidden w-0 h-0 p-0 border-0 resize-none" name="lower-tab5-clipboard"></textarea>
<textarea id="lower-tab6-clipboard" class="hidden overflow-hidden w-0 h-0 p-0 border-0 resize-none" name="lower-tab6-clipboard"></textarea>

<?php Functions::includeFile(file: '/app/Views/modals/collectionCreateModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/collectionUpdateModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/collectionDeleteModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/testCreateModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/testUpdateModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/testDeleteModal.php'); ?>
<?php Functions::includeFile(file: '/app/Views/modals/variableViewModal.php'); ?>

<div id="modalOverlay" class="<?= $modalOverlayClass ?>"></div>

<script>
    var ajaxToken = $("input#ajaxToken").val();
    var formId = "requestManage";
</script>
<script src="/js/home-autocomplete.js"></script>
<script src="/js/home-bodyfiles.js"></script>
<script src="/js/home-clipboard.js"></script>
<script src="/js/home-dropdown.js"></script>
<script src="/js/home-request.js"></script>
<script src="/js/modal.js"></script>
<script src="/js/tablednd.js"></script>
<script src="/js/tabs.js"></script>
<script>
<?php
// Check if user is guest or settings have been set to defaults
if (str_ends_with($settings_home_leftSection, '%')) {
    echo "var initGuest = true;\n";
} else {
    echo "var initGuest = false;\n";
}

// Check if user has logged in
if (Application::app()->session()->get('home/layout/initUser')) {
    Application::app()->session()->set("home/layout/initUser", false);
    echo "var initUser = true;\n";
} else {
    echo "var initUser = false;\n";
}
?>
</script>
<script src="/js/borders.js"></script>
<?php if ($responseBodyEncoded): ?>
<script>
    var settings_json_indent = <?= $settings_json_indent ?>;
    var settings_json_trailingCommas = <?= $settings_json_trailingCommas ?>;
    var settings_json_quoteKeys = <?= $settings_json_quoteKeys ?>;
    var settings_json_linkUrls = <?= $settings_json_linkUrls ?>;
    var responseBodyEncoded = <?= $responseBodyEncoded ?? "''" ?>;
</script>
<script src="/js/home-json.js"></script>
<?php endif; ?>
