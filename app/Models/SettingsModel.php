<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Request;
use App\Exception\AppException;
use App\Traits\FormTrait;

class SettingsModel extends Model
{
    use FormTrait;

    protected ?int $id = null;
    protected ?int $userId = null;
    protected ?array $userSettings = null;
    protected ?array $globalVariables = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;

    // These don't exist in the table
    protected ?bool $home_hidePasswords = null;
    protected ?string $http_defaultScheme = null;
    protected ?float $http_timeout  = null;
    protected ?string $http_version  = null;
    protected ?string $http_accept  = null;
    protected ?bool $http_sortHeaders = null;
    protected ?string $json_lineNumbers = null;
    protected ?int $json_indent = null;
    protected ?bool $json_linkUrls = null;
    protected ?bool $json_trailingCommas = null;
    protected ?bool $json_quoteKeys = null;
    protected ?bool $groups_stopOnResponseFail = null;
    protected ?bool $variables_showGlobalsHome = null;

    public function __construct()
    {
        // Check for no settings record - this is for guest users and new users when registering
        if (!$this->id) {
            // Get default settings and set properties
            $settingsFile = '/app/Data/settings.php';
            $settings = includeFile(file: $settingsFile, message: "Default settings file is missing: $settingsFile");
            $this->userSettings = $settings;
            $this->globalVariables = [];
        }
    }

    public static function tableName(): string
    {
        return 'settings';
    }

    static public function primaryKey(): array
    {
        return [
            'property' => 'id',
            'column' => 'id',
        ];
    }

    static public function fields(): array
    {
        return [
            'userId' => 'user_id',
            'userSettings' => 'user_settings',
            'globalVariables' => 'global_variables',
        ];
    }

    public function fieldLabels(): array
    {
        return [
            'home_hidePasswords' => 'hide passwords',
            'http_defaultScheme' => 'default scheme',
            'http_timeout' => 'timeout',
            'http_version' => 'version',
            'http_accept' => 'accept header',
            'http_sortHeaders' => 'sort headers',
            'json_lineNumbers' => 'line numbers',
            'json_indent' => 'indent',
            'json_linkUrls' => 'link URLs',
            'json_trailingCommas' => 'trailing commas',
            'json_quoteKeys' => 'quote keys',
            'groups_stopOnResponseFail' => 'stop on response fail',
            'variables_showGlobalsHome' => 'show on Home page',
        ];
    }

    public function rules(): array
    {
        return [
            'home_hidePasswords' => [self::RULE_REQUIRED],
            'http_defaultScheme' => [self::RULE_REQUIRED],
            'http_timeout' => [self::RULE_REQUIRED, self::RULE_DECIMAL, [self::RULE_MIN_VALUE, 'min' => 0.5], [self::RULE_MAX_VALUE, 'max' => 10]],
            'http_version' => [self::RULE_REQUIRED],
            'http_accept' => [self::RULE_REQUIRED],
            'http_sortHeaders' => [self::RULE_REQUIRED],
            'json_lineNumbers' => [self::RULE_REQUIRED],
            'json_indent' => [self::RULE_REQUIRED, self::RULE_INTEGER, [self::RULE_MIN_VALUE, 'min' => 2], [self::RULE_MAX_VALUE, 'max' => 10]],
            'json_linkUrls' => [self::RULE_REQUIRED],
            'json_trailingCommas' => [self::RULE_REQUIRED],
            'json_quoteKeys' => [self::RULE_REQUIRED],
            'groups_stopOnResponseFail' => [self::RULE_REQUIRED],
            'variables_showGlobalsHome' => [self::RULE_REQUIRED],
        ];
    }

    public function fetchData(): void
    {
        // Get settings
        $settingsData = self::getSingleRecord(['userId' => user()->id()]);

        // Check if record is missing - ID value won't be present
        if (!$settingsData->id) {
            // Create default settings
            self::createDefaults(user()->id());

            // Fetch data again
            $settingsData = self::getSingleRecord(['userId' => user()->id()]);
        }

        // Get user settings
        $settings = $settingsData->getProperty('userSettings');

        // Get default settings
        $settingsFile = '/app/Data/settings.php';
        $settingsDefaults = includeFile(file: $settingsFile, message: "Default settings file is missing: $settingsFile");

        // Update model properties
        $this->id = $settingsData->getProperty('id') ?? null;
        $this->userId = $settingsData->getProperty('userId') ?? null;
        $this->userSettings = $settingsData->getProperty('userSettings') ?? null;
        $this->globalVariables = $settingsData->getProperty('globalVariables') ?? null;
        $this->createdAt = $settingsData->getProperty('createdAt') ?? null;
        $this->updatedAt = $settingsData->getProperty('updatedAt') ?? null;

        // These don't exist in the table
        // If not present, use the defaults
        $this->home_hidePasswords = $settings['home']['hidePasswords'] ?? $settingsDefaults['home']['hidePasswords'];
        $this->http_defaultScheme = $settings['http']['defaultScheme'] ?? $settingsDefaults['http']['defaultScheme'];
        $this->http_timeout = $settings['http']['timeout'] ?? $settingsDefaults['http']['timeout'];
        $this->http_version = $settings['http']['version'] ?? $settingsDefaults['http']['version'];
        $this->http_accept = $settings['http']['accept'] ?? $settingsDefaults['http']['accept'];
        $this->http_sortHeaders = $settings['http']['sortHeaders'] ?? $settingsDefaults['http']['sortHeaders'];
        $this->json_lineNumbers = $settings['json']['lineNumbers'] ?? $settingsDefaults['json']['lineNumbers'];
        $this->json_indent = $settings['json']['indent'] ?? $settingsDefaults['json']['indent'];
        $this->json_linkUrls = $settings['json']['linkUrls'] ?? $settingsDefaults['json']['linkUrls'];
        $this->json_trailingCommas = $settings['json']['trailingCommas'] ?? $settingsDefaults['json']['trailingCommas'];
        $this->json_quoteKeys = $settings['json']['quoteKeys'] ?? $settingsDefaults['json']['quoteKeys'];
        $this->groups_stopOnResponseFail = $settings['groups']['stopOnResponseFail'] ?? $settingsDefaults['groups']['stopOnResponseFail'];
        $this->variables_showGlobalsHome = $settings['variables']['showGlobalsHome'] ?? $settingsDefaults['variables']['showGlobalsHome'];

        // Round timeout to 1 decimal place
        if ($this->http_timeout) {
            $this->http_timeout = round($this->http_timeout, 1);
        }
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody());

        // Get additional post data that is not defined as properties
        $postData = controller()->data();

        // Save to session - tabs
        session()->set('settings/selectedTab', $postData['selectedTab']);

        // Cast select elements to boolean
        // We need this before validation in case validation fails, so we can retain the select values on the form
        $this->home_hidePasswords = (bool)($this->home_hidePasswords);
        $this->http_sortHeaders = (bool)($this->http_sortHeaders);
        $this->json_linkUrls = (bool)($this->json_linkUrls);
        $this->json_trailingCommas = (bool)($this->json_trailingCommas);
        $this->json_quoteKeys = (bool)($this->json_quoteKeys);
        $this->groups_stopOnResponseFail = (bool)($this->groups_stopOnResponseFail);
        $this->variables_showGlobalsHome = (bool)($this->variables_showGlobalsHome);

        // Get table data
        $globalVariableEnabledArray = $postData['globalVariableEnabled'];
        $globalVariableNameArray = $postData['globalVariableName'];
        $globalVariableValueArray = $postData['globalVariableValue'];

        // Process table data
        $globalVariablesInputs = [];
        foreach ($globalVariableNameArray as $inputIndex => $inputName) {
            $inputEnabled = $globalVariableEnabledArray[$inputIndex];
            $inputValue = $globalVariableValueArray[$inputIndex];
            if ($inputEnabled) {
                $globalVariablesInputs[] = [
                    'name' => trim($inputName),
                    'value' => trim($inputValue),
                    'enabled' => $inputEnabled,
                ];
            }
        }

        // Set properties
        $this->globalVariables = $globalVariablesInputs;

        // Validate
        if ($this->validate()) {
            // Load settings array
            $settings = user()->getProperty('settings');

            // Since the form fields are not present in the table, we need to build JSON data from the field labels
            $fieldLabels = $this->fieldLabels();

            // Process field names and update array
            foreach (array_keys($fieldLabels) as $fieldName) {
                // Get value
                $fieldValue = $this->$fieldName;

                // Check for HTTP timeout - round to 1 decimal place - this will also cast to a int/float
                if ($fieldName == 'http_timeout') $fieldValue = round($fieldValue, 1);

                // Cast variable fields
                if ($fieldName == 'json_indent') $fieldValue = (int)($fieldValue);

                // Split field name into key / subkey
                $fieldKeys = explode('_', $fieldName);
                $fieldKey = $fieldKeys[0];
                $fieldSubkey = $fieldKeys[1];

                // Update array value
                $settings[$fieldKey][$fieldSubkey] = $fieldValue;
            }

            // Update settings property
            $this->userSettings = $settings;

            // Save data
            if ($this->updateRecord()) {
                // Set flash message
                session()->setFlash('info', 'Settings have been updated.');

                // Redirect to settings page
                response()->redirect('/settings');
            }
        } else {
            // Set flash with removal set to true, otherwise it will be shown again on page reload
            session()->setFlash('warning', 'Settings failed to update.', true);
        }
    }

    public static function createDefaults(int $userId): void
    {
        // Get default settings and encode
        $settingsFile = '/app/Data/settings.php';
        $settings = includeFile(file: $settingsFile, message: "Default settings file is missing: $settingsFile");

        // Get existing settings
        $settingsData = self::getSingleRecord(['userId' => $userId]);

        // Set defaults
        $settingsData->setProperty('userSettings', $settings);

        // Check if record exists
        if ($settingsData->id) {
            // Update default settings
            $settingsData->updateRecord();
        } else {
            // Insert default settings
            $settingsData->userId = $userId;
            $settingsData->insertRecord();
        }
    }

    public static function checkSetting(string $key, ?array $settings = null): mixed
    {
        // !!! do not change the {{value}} style output here to returning "null" or "false"
        // that will break setting keys which actually use "null" or "false" for values

        // Get settings if not provided
        if (!$settings) $settings = user()->getProperty('settings');

        // Turn key into array
        $keyArray = explode('/', $key);

        // Process keys
        foreach ($keyArray as $keyItem) {
            // Confirm array key exists
            if (array_key_exists($keyItem, $settings)) {
                // Update setting variable
                $settings = $settings[$keyItem];

                // Check for empty or null
                if ($settings === '' || $settings === null) {
                    return '{{null}}';
                }
            } else {
                return '{{missing}}';
            }
        }

        // Return
        return $settings;
    }

    public static function getSetting(string $key, bool $allowNull = false): mixed
    {
        // Check setting
        $setting = self::checkSetting($key);

        // Check for missing or null
        if ($setting === '{{missing}}' || $setting === '{{null}}') {
            // If not allowing nulls and user is logged in
            if (!$allowNull && UserModel::isLoggedIn()) {
                // Get default settings
                $settingsFile = '/app/Data/settings.php';
                $settings = includeFile(file: $settingsFile, message: "Default settings file is missing: $settingsFile");

                // Check setting again
                $setting = self::checkSetting($key, $settings);

                // Check for missing or null again
                if ($setting === '{{missing}}' || $setting === '{{null}}') {
                    throw new AppException(message: "Setting is missing: $key");
                }

                // ------------------------------------------------------------------------------------------
                // Alternative where if a setting is missing, the settings are created again from defaults
                // Downside is that other settings are lost

                //// Create defaults
                //self::createDefaults(user()->id());

                //// We need to update the settings property in the the users instance
                //// so we can obtain the setting again below
                //user()->loadSettings();

                //// Get setting again
                //$setting = self::checkSetting($key);
                // ------------------------------------------------------------------------------------------
            // If not allowing nulls and user is guest
            } elseif (!$allowNull) {
                // Setting is missing from settings.php
                throw new AppException(message: "Setting is missing: $key");
            } else {
                // Return null
                return null;
            }
        }

        // Return
        return $setting;
    }

    public static function updateSetting(string $name, mixed $value): void
    {
        // Update a single user setting

        // Convert key to array
        $nameArray = explode('/', $name);

        // Get settings data
        $settingsData = self::getSingleRecord(['userId' => user()->id()]);

        // Get settings
        $settings = $settingsData->getProperty('userSettings');

        // Update settings
        if (array_key_exists(2, $nameArray)) {
            $settings[$nameArray[0]][$nameArray[1]][$nameArray[2]] = $value;
        } else {
            $settings[$nameArray[0]][$nameArray[1]] = $value;
        }

        // Update model property
        $settingsData->setProperty('userSettings', $settings);

        // Update record
        $settingsData->updateRecord();
    }

    public static function deleteSetting(string $name): void
    {
        // Delete a single user setting

        // Convert key to array
        $nameArray = explode('/', $name);

        // Get settings data
        $settingsData = self::getSingleRecord(['userId' => user()->id()]);

        // Get user settings
        $settings = $settingsData->getProperty('userSettings');

        // Update settings
        if (array_key_exists(2, $nameArray)) {
            unset($settings[$nameArray[0]][$nameArray[1]][$nameArray[2]]);
        } else {
            unset($settings[$nameArray[0]][$nameArray[1]]);
        }

        // Update model property
        $settingsData->setProperty('userSettings', $settings);

        // Update record
        $settingsData->updateRecord();
    }

    public static function variables(int $userId): array
    {
        $variables = [];

        // Get global variables
        $globalVariables = self::getSingleRecord(['userId' => $userId])->getProperty('globalVariables') ?? [];

        // Process global variables
        foreach ($globalVariables as $globalVariable) {
            if ($globalVariable['enabled'] == 'on') {
                $variables[$globalVariable['name']] = ['value' => $globalVariable['value'], 'type' => 'global'];
            }
        }

        return $variables;
    }
}
