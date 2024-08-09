<?php

declare(strict_types=1);

use App\Core\Application;

$title = 'Settings';

// Get tabs from session
$selectedTab = Application::app()->session()->get('settings/selectedTab') ?? "tab1";

// Set tabs CSS classes
$selectedTab == 'tab1' ? $selectedTab_tab1 = ' current' : $selectedTab_tab1 = null;
$selectedTab == 'tab2' ? $selectedTab_tab2 = ' current' : $selectedTab_tab2 = null;
$selectedTab == 'tab3' ? $selectedTab_tab3 = ' current' : $selectedTab_tab3 = null;
$selectedTab == 'tab4' ? $selectedTab_tab4 = ' current' : $selectedTab_tab4 = null;

// Get values for text inputs
$http_timeout = number_format(Application::app()->model('SettingsModel')->getProperty('http_timeout'), 1);
$json_indent = Application::app()->model('SettingsModel')->getProperty('json_indent');

// Create selected strings for the select inputs
Application::app()->model('SettingsModel')->getProperty('http_defaultScheme') == 'http://' ? $http_defaultScheme_http = ' selected' : $http_defaultScheme_http = null;
Application::app()->model('SettingsModel')->getProperty('http_defaultScheme') == 'https://' ? $http_defaultScheme_https = ' selected' : $http_defaultScheme_https = null;
Application::app()->model('SettingsModel')->getProperty('http_sortHeaders') === true ? $http_sortHeaders_on = ' selected' : $http_sortHeaders_on = null;
Application::app()->model('SettingsModel')->getProperty('http_sortHeaders') === false ? $http_sortHeaders_off = ' selected' : $http_sortHeaders_off = null;
Application::app()->model('SettingsModel')->getProperty('http_version') == '1.0' ? $http_version_10 = ' selected' : $http_version_10 = null;
Application::app()->model('SettingsModel')->getProperty('http_version') == '1.1' ? $http_version_11 = ' selected' : $http_version_11 = null;
Application::app()->model('SettingsModel')->getProperty('http_version') == '2.0' ? $http_version_20 = ' selected' : $http_version_20 = null;
Application::app()->model('SettingsModel')->getProperty('http_version') == 'auto' ? $http_version_auto = ' selected' : $http_version_auto = null;
Application::app()->model('SettingsModel')->getProperty('http_accept') == 'default' ? $http_accept_default = ' selected' : $http_accept_default = null;
Application::app()->model('SettingsModel')->getProperty('http_accept') == 'application/json' ? $http_accept_json = ' selected' : $http_accept_json = null;
Application::app()->model('SettingsModel')->getProperty('json_lineNumbers') == 'left' ? $json_lineNumbers_left = ' selected' : $json_lineNumbers_left = null;
Application::app()->model('SettingsModel')->getProperty('json_lineNumbers') == 'right' ? $json_lineNumbers_right = ' selected' : $json_lineNumbers_right = null;
Application::app()->model('SettingsModel')->getProperty('json_linkUrls') === true ? $json_linkUrls_on = ' selected' : $json_linkUrls_on = null;
Application::app()->model('SettingsModel')->getProperty('json_linkUrls') === false ? $json_linkUrls_off = ' selected' : $json_linkUrls_off = null;
Application::app()->model('SettingsModel')->getProperty('json_trailingCommas') === true ? $json_trailingCommas_on = ' selected' : $json_trailingCommas_on = null;
Application::app()->model('SettingsModel')->getProperty('json_trailingCommas') === false ? $json_trailingCommas_off = ' selected' : $json_trailingCommas_off = null;
Application::app()->model('SettingsModel')->getProperty('json_quoteKeys') === true ? $json_quoteKeys_on = ' selected' : $json_quoteKeys_on = null;
Application::app()->model('SettingsModel')->getProperty('json_quoteKeys') === false ? $json_quoteKeys_off = ' selected' : $json_quoteKeys_off = null;
Application::app()->model('SettingsModel')->getProperty('groups_stopOnResponseFail') === true ? $groups_stopOnResponseFail_on = ' selected' : $groups_stopOnResponseFail_on = null;
Application::app()->model('SettingsModel')->getProperty('groups_stopOnResponseFail') === false ? $groups_stopOnResponseFail_off = ' selected' : $groups_stopOnResponseFail_off = null;
Application::app()->model('SettingsModel')->getProperty('variables_showGlobalsHome') === true ? $variables_showGlobalsHome_on = ' selected' : $variables_showGlobalsHome_on = null;
Application::app()->model('SettingsModel')->getProperty('variables_showGlobalsHome') === false ? $variables_showGlobalsHome_off = ' selected' : $variables_showGlobalsHome_off = null;

// Get global variables
$globalVariablesInputs = Application::app()->model('SettingsModel')->getProperty('globalVariables');

// Get errors
$errors = Application::app()->model('SettingsModel')->getProperty('errors');
$http_timeoutError = Application::app()->model('SettingsModel')->getError('http_timeout');
$json_indentError = Application::app()->model('SettingsModel')->getError('json_indent');

// Get CSS class
$http_timeoutClass = Application::app()->model('SettingsModel')->getInputClass('http_timeout');
$json_indentClass = Application::app()->model('SettingsModel')->getInputClass('json_indent');

// Set tab location and field name for errors
$errorFields = [
    'http_timeout' => [
        'tab' => 'Request',
        'label' => 'Timeout',
    ],
    'json_indent' => [
        'tab' => 'Response',
        'label' => 'JSON indent',
    ],
];
?>
<div class="flex h-full">

    <form id="settings" action="/settings" method="POST" class="flex mx-auto">
        <div class="grid grid-cols-1 content-between mt-10 mb-10 w-[800px] overflow-y-auto p-8 bg-zinc-50 dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-650 rounded-xl">

            <div>
                <h2 class="text-2xl font-bold mb-5">Settings</h2>

                <div class="mb-10">
                    <div class="h-[39px]">
                        <div>
                            <ul id="settings" class="tabs">
                                <li id="tab1" class="tab-link<?= $selectedTab_tab1 ?>">Request</li>
                                <li id="tab2" class="tab-link<?= $selectedTab_tab2 ?>">Response</li>
                                <li id="tab3" class="tab-link<?= $selectedTab_tab3 ?>">Groups</li>
                                <li id="tab4" class="tab-link<?= $selectedTab_tab4 ?>">Variables</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div id="settings-content">
                    <div id="tab1-content" class="tab-content<?= $selectedTab_tab1 ?>">
                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Default scheme</div>
                            <div class="w-[225px]">
                                <select name="http_defaultScheme" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="http://"<?= $http_defaultScheme_http ?>>http://</option>
                                    <option value="https://"<?= $http_defaultScheme_https ?>>https://</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Timeout</div>
                            <div class="w-[225px]">
                                <input type="text" name="http_timeout" value="<?= $http_timeout ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $http_timeoutClass ?>"/>
                            </div>
                            <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $http_timeoutError ?></div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">HTTP version</div>
                            <div class="w-[225px]">
                                <select name="http_version" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1.0"<?= $http_version_10 ?>>1.0</option>
                                    <option value="1.1"<?= $http_version_11 ?>>1.1</option>
                                    <option value="2.0"<?= $http_version_20 ?>>2.0</option>
                                    <option value="auto"<?= $http_version_auto ?>>Auto</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Accept header</div>
                            <div class="w-[225px]">
                                <select name="http_accept" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="default"<?= $http_accept_default ?>>Default</option>
                                    <option value="application/json"<?= $http_accept_json ?>>application/json</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="tab2-content" class="tab-content<?= $selectedTab_tab2 ?>">
                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Sort headers</div>
                            <div class="w-[225px]">
                                <select name="http_sortHeaders" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1"<?= $http_sortHeaders_on ?>>On</option>
                                    <option value="0"<?= $http_sortHeaders_off ?>>Off</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Line numbers</div>
                            <div class="w-[225px]">
                                <select name="json_lineNumbers" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="left"<?= $json_lineNumbers_left ?>>Left</option>
                                    <option value="right"<?= $json_lineNumbers_right ?>>Right</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">JSON indent</div>
                            <div class="w-[225px]">
                                <input type="text" name="json_indent" value="<?= $json_indent ?>" autocomplete="one-time-code" spellcheck="false" class="<?= $json_indentClass ?>"/>
                            </div>
                            <div class="ml-4 content-center text-red-500 dark:text-red-500"><?= $json_indentError ?></div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">JSON URL links</div>
                            <div class="w-[225px]">
                                <select name="json_linkUrls" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1"<?= $json_linkUrls_on ?>>On</option>
                                    <option value="0"<?= $json_linkUrls_off ?>>Off</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">JSON trailing commas</div>
                            <div class="w-[225px]">
                                <select name="json_trailingCommas" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1"<?= $json_trailingCommas_on ?>>On</option>
                                    <option value="0"<?= $json_trailingCommas_off ?>>Off</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">JSON quote keys</div>
                            <div class="w-[225px]">
                                <select name="json_quoteKeys" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1"<?= $json_quoteKeys_on ?>>On</option>
                                    <option value="0"<?= $json_quoteKeys_off ?>>Off</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="tab3-content" class="tab-content<?= $selectedTab_tab3 ?>">
                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Stop on response fail</div>
                            <div class="w-[225px]">
                                <select name="groups_stopOnResponseFail" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1"<?= $groups_stopOnResponseFail_on ?>>On</option>
                                    <option value="0"<?= $groups_stopOnResponseFail_off ?>>Off</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="tab4-content" class="tab-content<?= $selectedTab_tab4 ?>">
                        <div class="flex flex-row mb-6">
                            <div class="w-[175px] mr-4 text-right content-center">Show on Home page</div>
                            <div class="w-[225px]">
                                <select name="variables_showGlobalsHome" class="input-normal w-full rounded p-2 border border-zinc-300 dark:border-zinc-650 bg-white dark:bg-black">
                                    <option value="1"<?= $variables_showGlobalsHome_on ?>>On</option>
                                    <option value="0"<?= $variables_showGlobalsHome_off ?>>Off</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="mb-2">Global variables</div>
                            <div>
                                <table id="globalVariablesInputs" class="table-auto text-left text-sm bg-white dark:bg-black">
                                    <thead>
                                    <tr class="h-8">
                                        <th class="w-10 px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                        <th class="w-[200px] px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Name</th>
                                        <th class="w-[400px] px-2 py-0 font-semibold border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800">Value</th>
                                        <th colspan="2" class="px-2 py-0 border border-zinc-300 dark:border-zinc-650 bg-zinc-100 dark:bg-zinc-800"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($globalVariablesInputs as $globalVariablesInput): ?>
                                        <?php
                                        $globalVariableName = $globalVariablesInput['name'];
                                        $globalVariableValue = $globalVariablesInput['value'];
                                        $globalVariableEnabled = $globalVariablesInput['enabled'];
                                        $globalVariableEnabled == 'on' ? $globalVariableEnabledCheckbox = ' checked' : $globalVariableEnabledCheckbox = null;
                                        $globalVariableName = htmlspecialchars($globalVariableName);
                                        $globalVariableValue = htmlspecialchars($globalVariableValue);
                                        ?>
                                        <!--Existing row-->
                                        <tr class="h-8">
                                            <td class="pt-0 text-center border border-zinc-300 dark:border-zinc-650">
                                                <input type="checkbox" name="globalVariableCheckbox[]" class="bg-transparent dark:bg-transparent"<?= $globalVariableEnabledCheckbox ?>>
                                                <input type="hidden" name="globalVariableEnabled[]" value="<?= $globalVariableEnabled ?>">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="globalVariableName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" value="<?= $globalVariableName ?>" class="w-full px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
                                            <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                                <input type="text" name="globalVariableValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" value="<?= $globalVariableValue ?>" class="w-full px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                            </td>
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
                                            <input type="checkbox" name="globalVariableCheckbox[]" class="bg-transparent dark:bg-transparent" disabled>
                                            <input type="hidden" name="globalVariableEnabled[]">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="globalVariableName[]" autocomplete="one-time-code" placeholder="Name" spellcheck="false" class="w-full px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
                                        </td>
                                        <td class="px-1 py-1 border border-zinc-300 dark:border-zinc-650">
                                            <input type="text" name="globalVariableValue[]" autocomplete="one-time-code" placeholder="Value" spellcheck="false" class="w-full px-1 py-0 text-sm font-mono border-0 focus:ring-0 bg-transparent dark:bg-transparent placeholder-zinc-300 dark:placeholder-zinc-600 outline-none">
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
                    </div>

                </div>

                <div>
                    <input type="hidden" name="id" value="<?= Application::app()->model('SettingsModel')->getProperty('id') ?>">
                    <input type="hidden" name="userId" value="<?= Application::app()->model('SettingsModel')->getProperty('userId') ?>">
                    <input type="hidden" name="selectedTab" value="<?= $selectedTab ?>">
                </div>
            </div>

            <div>
                <div class="mb-10">
                <?php if ($errors): ?>
                    <div class="mb-2 font-bold text-red-600 dark:text-red-700">These fields are not valid:</div>
                    <?php foreach (array_keys($errors) as $errorInput): ?>
                        <div><?= $errorFields[$errorInput]['tab'] . ' <i class="fa-solid fa-arrow-right-long"></i> ' . $errorFields[$errorInput]['label'] ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
                <div class="flex flex-row justify-between">
                    <div class="flex">
                        <button type="submit" class="mr-5 primary">Update</button>
                        <button type="button" onClick="window.location.href='/settings';" class="secondary">Revert</button>
                    </div>
                    <div>
                        <button type="button" onClick="window.location.href='/settings/defaults';" class="secondary">Defaults</button>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

<script>
    var ajaxToken = $("input#ajaxToken").val();
</script>
<script src="/js/settings.js"></script>
<script src="/js/tablednd.js"></script>
