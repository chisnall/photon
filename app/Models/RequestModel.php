<?php

namespace App\Models;

use App\Core\Application;
use App\Core\Model;
use App\Core\Request;
use App\Http\HttpClient;
use App\Http\HttpTest;
use App\Traits\FormTrait;

class RequestModel extends Model
{
    use FormTrait;

    protected ?int $id = null;
    protected ?int $collectionId = null;
    protected ?string $requestMethod = null;
    protected ?string $requestUrl = null;
    protected ?string $requestName = null;
    protected ?array $requestParamsInputs = null;
    protected ?array $requestHeadersInputs = null;
    protected ?string $requestAuth = null;
    protected ?string $requestAuthBasicUsername = null;
    protected ?string $requestAuthBasicPassword = null;
    protected ?string $requestAuthTokenValue = null;
    protected ?string $requestAuthHeaderName = null;
    protected ?string $requestAuthHeaderValue = null;
    protected ?string $requestBody = null;
    protected ?string $requestBodyTextValue = null;
    protected ?string $requestBodyTextType = null;
    protected ?array $requestBodyFormInputs = null;
    protected ?array $requestBodyFormSend = null;
    protected ?string $requestBodyFile = null;
    protected ?array $requestVariablesInputs = null;
    protected ?array $testsResults = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;
    protected ?string $formAction = null;

    public static function tableName(): string
    {
        return 'requests';
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
            'collectionId' => 'collection_id',
            'requestMethod' => 'request_method',
            'requestUrl' => 'request_url',
            'requestName' => 'request_name',
            'requestParamsInputs' => 'request_params_inputs',
            'requestHeadersInputs' => 'request_headers_inputs',
            'requestAuth' => 'request_auth',
            'requestAuthBasicUsername' => 'request_auth_basic_username',
            'requestAuthBasicPassword' => 'request_auth_basic_password',
            'requestAuthTokenValue' => 'request_auth_token_value',
            'requestAuthHeaderName' => 'request_auth_header_name',
            'requestAuthHeaderValue' => 'request_auth_header_value',
            'requestBody' => 'request_body',
            'requestBodyTextValue' => 'request_body_text_value',
            'requestBodyTextType' => 'request_body_text_type',
            'requestBodyFormInputs' => 'request_body_form_inputs',
            'requestBodyFile' => 'request_body_file',
            'requestVariablesInputs' => 'request_variables_inputs',
        ];
    }

    public function fieldLabels(): array
    {
        return [
            'collectionId' => 'collection ID',
            'requestMethod' => 'request method',
            'requestUrl' => 'request URL',
            'requestName' => 'request name',
            'requestParamsInputs' => 'params',
            'requestHeadersInputs' => 'headers',
            'requestAuth' => 'auth',
            'requestAuthBasicUsername' => 'auth username',
            'requestAuthBasicPassword' => 'auth password',
            'requestAuthTokenValue' => 'auth token',
            'requestAuthHeaderName' => 'auth header name',
            'requestAuthHeaderValue' => 'auth header value',
            'requestBody' => 'request body',
            'requestBodyTextValue' => 'body text',
            'requestBodyTextType' => 'body text type',
            'requestBodyFormInputs' => 'body form',
            'requestBodyFile' => 'body file',
            'requestVariablesInputs' => 'variables',
        ];
    }

    public function rules(): array
    {
        $rules = [
            'requestMethod' => [self::RULE_REQUIRED],
            'requestUrl' => [self::RULE_REQUIRED],
        ];

        // This rule is conditional, if the auth is set to "basic"
        if ($this->requestAuth == 'basic') {
            $rules['requestAuthBasicUsername'] = [self::RULE_REQUIRED];
            $rules['requestAuthBasicPassword'] = [self::RULE_REQUIRED];
        }

        // This rule is conditional, if the auth is set to "token"
        if ($this->requestAuth == 'token') {
            $rules['requestAuthTokenValue'] = [self::RULE_REQUIRED];
        }

        // This rule is conditional, if the auth is set to "header"
        if ($this->requestAuth == 'header') {
            $rules['requestAuthHeaderName'] = [self::RULE_REQUIRED];
            $rules['requestAuthHeaderValue'] = [self::RULE_REQUIRED];
        }

        // This rule is conditional, if the body type is set to "text"
        if ($this->requestBody == 'text') {
            $rules['requestBodyTextValue'] = [self::RULE_REQUIRED];

            // Additional rule if text is JSON
            if ($this->requestBodyTextType == 'json') {
                $rules['requestBodyTextValue'][] = self::RULE_JSON;
            }
        }

        // This rule is conditional, if the body type is set to "form"
        if ($this->requestBody == 'form') {
            $rules['requestBodyFormSend'] = [self::RULE_REQUIRED];
        }

        // This rule is conditional, if the body type is set to "file"
        if ($this->requestBody == 'file') {
            $rules['requestBodyFile'] = [self::RULE_REQUIRED];
        }

        // This rule is conditional, if the form action type is save
        if ($this->formAction == 'save') {
            $rules['collectionId'] = [self::RULE_REQUIRED];
        }

        return $rules;
    }

    public function displayError(): ?string
    {
        // Get errors
        $collectionIdError = $this->getError('collectionId');
        $requestUrlError = $this->getError('requestUrl');
        $requestAuthBasicUsernameError = $this->getError('requestAuthBasicUsername');
        $requestAuthBasicPasswordError = $this->getError('requestAuthBasicPassword');
        $requestAuthTokenValueError = $this->getError('requestAuthTokenValue');
        $requestAuthHeaderNameError = $this->getError('requestAuthHeaderName');
        $requestAuthHeaderValueError = $this->getError('requestAuthHeaderValue');
        $requestBodyTextValueError = $this->getError('requestBodyTextValue');
        $requestBodyFormError = $this->getError('requestBodyFormSend');
        $requestBodyFileError = $this->getError('requestBodyFile');

        // Collection
        if ($collectionIdError) {
            if ($collectionIdError == 'This field is required') $collectionIdError = 'no collection selected';
            return $collectionIdError;
        }

        // Request URL
        if ($requestUrlError) {
            if ($requestUrlError == 'This field is required') $requestUrlError = 'enter the URL';
            elseif ($requestUrlError == 'Must be a valid URL') $requestUrlError = 'the URL is not valid';
            return $requestUrlError;
        }

        // Auth - basic - username
        if ($requestAuthBasicUsernameError) {
            if ($requestAuthBasicUsernameError == 'This field is required') $requestAuthBasicUsernameError = 'enter the username';
            return $requestAuthBasicUsernameError;
        }

        // Auth - basic - password
        if ($requestAuthBasicPasswordError) {
            if ($requestAuthBasicPasswordError == 'This field is required') $requestAuthBasicPasswordError = 'enter the password';
            return $requestAuthBasicPasswordError;
        }

        // Auth - token
        if ($requestAuthTokenValueError) {
            if ($requestAuthTokenValueError == 'This field is required') $requestAuthTokenValueError = 'enter the token';
            return $requestAuthTokenValueError;
        }

        // Auth - header - name
        if ($requestAuthHeaderNameError) {
            if ($requestAuthHeaderNameError == 'This field is required') $requestAuthHeaderNameError = 'enter the header name';
            return $requestAuthHeaderNameError;
        }

        // Auth - header - value
        if ($requestAuthHeaderValueError) {
            if ($requestAuthHeaderValueError == 'This field is required') $requestAuthHeaderValueError = 'enter the header value';
            return $requestAuthHeaderValueError;
        }

        // Request body text
        if ($requestBodyTextValueError) {
            if ($requestBodyTextValueError == 'This field is required') $requestBodyTextValueError = 'enter the body text';
            if ($requestBodyTextValueError == 'Must be valid JSON') $requestBodyTextValueError = 'the body text is not valid JSON';
            return $requestBodyTextValueError;
        }

        // Request body form
        if ($requestBodyFormError) {
            if ($requestBodyFormError == 'This field is required') $requestBodyFormError = 'enter the form data';
            return $requestBodyFormError;
        }

        // Request body file
        if ($requestBodyFileError) {
            if ($requestBodyFileError == 'This field is required') $requestBodyFileError = 'select the body file';
            return $requestBodyFileError;
        }

        return null;
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody(false));

        // Delete - this does not require validation
        if ($this->formAction == 'delete') {
            // Delete record
            $this->deleteRecord();

            // Clear session
            self::clearSession();

            // Update settings
            SettingsModel::deleteSetting("home/left/requestId");

            // Set flash with removal set to true, otherwise it will be shown again on page reload
            Application::app()->session()->setFlash('info', 'Request has been deleted.', true);

            // Return now
            return;
        }

        // Save to session - request URL - the URL is processed below
        Application::app()->session()->set('home/upper/requestUrl', $this->requestUrl);

        // For auth header value, remove CRLF and LF
        if ($this->requestAuthHeaderValue) {
            $this->requestAuthHeaderValue = str_replace(["\r", "\n"], '', $this->requestAuthHeaderValue);
        }

        // For body text, convert CRLF to LF
        if ($this->requestBodyTextValue) {
            $this->requestBodyTextValue = str_replace("\r\n", "\n", $this->requestBodyTextValue);
        }

        // Check for existing file upload
        $requestBodyFileExisting = $_POST['requestBodyFileExisting'] ?? null;
        $this->requestBodyFile = $requestBodyFileExisting;

        // Check for new file upload
        $requestBodyFile = $_FILES['requestBodyFile'];
        if ($requestBodyFile['size'] > 0) {
            // Set uploaded body files directory
            $uploadedBodyFilesDirectory = UPLOAD_PATH . '/' . Application::app()->user()->id();

            // Set new file path
            $requestBodyFilePath = $uploadedBodyFilesDirectory . '/' . $requestBodyFile['name'];

            // Check upload directory exists - base directory
            if (!file_exists(UPLOAD_PATH)) {
                mkdir(UPLOAD_PATH);
            }

            // Check upload directory exists
            if (!file_exists($uploadedBodyFilesDirectory)) {
                mkdir($uploadedBodyFilesDirectory);
            }

            // Move temp file
            move_uploaded_file($requestBodyFile['tmp_name'], $requestBodyFilePath);

            // Set property
            $this->requestBodyFile = $requestBodyFilePath;
        }

        // Handle tables

        // Get table data
        $requestParamEnabledArray = $_POST['requestParamEnabled'];
        $requestParamNameArray = $_POST['requestParamName'];
        $requestParamValueArray = $_POST['requestParamValue'];
        $requestHeaderEnabledArray = $_POST['requestHeaderEnabled'];
        $requestHeaderNameArray = $_POST['requestHeaderName'];
        $requestHeaderValueArray = $_POST['requestHeaderValue'];
        $requestBodyFormInputEnabledArray = $_POST['requestBodyFormInputEnabled'];
        $requestBodyFormInputNameArray = $_POST['requestBodyFormInputName'];
        $requestBodyFormInputValueArray = $_POST['requestBodyFormInputValue'];
        $requestVariableEnabledArray = $_POST['requestVariableEnabled'];
        $requestVariableKeyArray = $_POST['requestVariableKey'];
        $requestVariableNameArray = $_POST['requestVariableName'];

        // Process table based inputs
        $requestParamsInputs = $this->processTable($requestParamEnabledArray, $requestParamNameArray, $requestParamValueArray);
        $requestHeadersInputs = $this->processTable($requestHeaderEnabledArray, $requestHeaderNameArray, $requestHeaderValueArray);
        $requestBodyFormInputs = $this->processTable($requestBodyFormInputEnabledArray, $requestBodyFormInputNameArray, $requestBodyFormInputValueArray);

        // Process variables
        $requestVariablesInputs = $this->processVariables($requestVariableEnabledArray, $requestVariableKeyArray, $requestVariableNameArray);

        // We need to determine the form send data for the form validation
        $requestBodyFormSend = [];
        foreach ($requestBodyFormInputs as $requestBodyFormInput) {
            $requestBodyFormInputEnabled = $requestBodyFormInput['enabled'];
            $requestBodyFormInputName = $requestBodyFormInput['name'];
            $requestBodyFormInputValue = $requestBodyFormInput['value'];

            if ($requestBodyFormInputEnabled == 'on' && $requestBodyFormInputName != '' && $requestBodyFormInputValue != '') {
                $requestBodyFormSend[$requestBodyFormInputName] = $requestBodyFormInputValue;
            }
        }

        // Set properties
        $this->requestParamsInputs = $requestParamsInputs;
        $this->requestHeadersInputs = $requestHeadersInputs;
        $this->requestBodyFormInputs = $requestBodyFormInputs;
        $this->requestBodyFormSend = $requestBodyFormSend;
        $this->requestVariablesInputs = $requestVariablesInputs;

        // Save to session - inputs
        Application::app()->session()->set('home/upper/requestMethod', $this->requestMethod);
        Application::app()->session()->set('home/upper/requestName', $this->requestName);
        Application::app()->session()->set('home/upper/requestParamsInputs', $this->requestParamsInputs);
        Application::app()->session()->set('home/upper/requestHeadersInputs', $this->requestHeadersInputs);
        Application::app()->session()->set('home/upper/requestAuth', $this->requestAuth);
        Application::app()->session()->set('home/upper/requestAuthBasicUsername', $this->requestAuthBasicUsername);
        Application::app()->session()->set('home/upper/requestAuthBasicPassword', $this->requestAuthBasicPassword);
        Application::app()->session()->set('home/upper/requestAuthTokenValue', $this->requestAuthTokenValue);
        Application::app()->session()->set('home/upper/requestAuthHeaderName', $this->requestAuthHeaderName);
        Application::app()->session()->set('home/upper/requestAuthHeaderValue', $this->requestAuthHeaderValue);
        Application::app()->session()->set('home/upper/requestBody', $this->requestBody);
        Application::app()->session()->set('home/upper/requestBodyTextValue', $this->requestBodyTextValue);
        Application::app()->session()->set('home/upper/requestBodyTextType', $this->requestBodyTextType);
        Application::app()->session()->set('home/upper/requestBodyFormInputs', $this->requestBodyFormInputs);
        Application::app()->session()->set('home/upper/requestBodyFileExisting', $this->requestBodyFile);
        Application::app()->session()->set('home/upper/requestVariablesInputs', $this->requestVariablesInputs);

        // Save to session - tabs
        Application::app()->session()->set('home/left/selectedTab', $_POST['left_selectedTab']);
        Application::app()->session()->set('home/upper/selectedTab', $_POST['upper_selectedTab']);
        Application::app()->session()->set('home/lower/selectedTab', $_POST['lower_selectedTab']);

        // Validate request
        if ($this->validate()) {
            // Remove request error
            Application::app()->session()->remove('tests/upper/requestError');

            // Send
            if ($this->formAction == 'send') {
                // Create new client
                $client = new HttpClient($this, true);
                $client->request();

                // Confirm response is valid
                if ($client->getProperty('responseValid')) {
                    // Run tests
                    $this->handleTests($client);
                }
            }

            // Save
            if ($this->formAction == 'save') {
                // If request name is missing, set it to the request URL
                if (!$this->requestName) $this->requestName = $this->requestUrl;

                // Determine if record is being updated or created
                if ($this->id) {
                    // Update record
                    $this->updateRecord();

                    // Set flash with removal set to true, otherwise it will be shown again on page reload
                    Application::app()->session()->setFlash('info', 'Request has been updated.', true);
                } else {
                    // Insert record
                    $this->insertRecord();

                    // Register session variables
                    Application::app()->session()->set('home/left/requestId', $this->id);

                    // Set flash with removal set to true, otherwise it will be shown again on page reload
                    Application::app()->session()->setFlash('info', 'Request has been created.', true);
                }

                // Register session variables
                Application::app()->session()->set('home/left/requestName', $this->requestName);
                Application::app()->session()->set('home/upper/requestModified', false);
            }

            // Clone
            if ($this->formAction == 'clone' && $this->id) {
                // If request name is missing, set it to the request URL
                if (!$this->requestName) $this->requestName = $this->requestUrl;

                // Get tests data
                $testsData = TestModel::getAllRecords(match: ['requestId' => $this->id], sort: ['testName' => 'ASC']);

                // Insert record
                $this->insertRecord();

                // Check for tests records from above and duplicate them
                if ($testsData) {
                    foreach ($testsData as $testModel) {
                        $testModel->setProperty('requestId', $this->id);
                        $testModel->insertRecord();
                    }
                }

                // Register session variables
                Application::app()->session()->set('home/left/requestId', $this->id);
                Application::app()->session()->set('home/left/requestName', $this->requestName);
                Application::app()->session()->set('home/upper/requestModified', false);

                // Update settings
                SettingsModel::updateSetting('home/left/requestId', $this->id);

                // Set flash with removal set to true, otherwise it will be shown again on page reload
                Application::app()->session()->setFlash('info', 'Request has been cloned.', true);
            }
        } else {
            // Register session variables
            if ($this->id) Application::app()->session()->set('home/upper/requestModified', true);
            Application::app()->session()->set('tests/upper/requestError', $this->displayError());

            // Remove the response from the session
            Application::app()->session()->remove('response');

            // Set flash with removal set to true, otherwise it will be shown again on page reload
            Application::app()->session()->setFlash('warning', 'Error: ' . $this->displayError(), true);
        }
    }

    public function processTable(array $enabledArray, $nameArray, $valueArray): array
    {
        // For each of the table based inputs, the final line will be included and will
        // be empty, so we need to check for that.
        // Don't check name or value - one of those could be empty.
        // Check the enabled value instead since that is always present.

        $inputs = [];

        foreach ($nameArray as $inputIndex => $inputName) {
            $inputEnabled = $enabledArray[$inputIndex];
            $inputValue = $valueArray[$inputIndex];
            if ($inputEnabled) {
                $inputs[] = [
                    'name' => trim($inputName),
                    'value' => trim($inputValue),
                    'enabled' => $inputEnabled,
                ];
            }
        }

        return $inputs;
    }

    public function processVariables(array $enabledArray, $keyArray, $nameArray): array
    {
        // For each of the table based inputs, the final line will be included and will
        // be empty, so we need to check for that.
        // Don't check key or name - one of those could be empty.
        // Check the enabled value instead since that is always present.

        $inputs = [];

        foreach ($keyArray as $inputIndex => $inputKey) {
            $inputEnabled = $enabledArray[$inputIndex];
            $inputName = $nameArray[$inputIndex];
            if ($inputEnabled) {
                $inputs[] = [
                    'key' => trim($inputKey),
                    'name' => trim($inputName),
                    'enabled' => $inputEnabled,
                ];
            }
        }

        return $inputs;
    }

    public function handleTests(HttpClient $client): void
    {
        // Check for ID (request has been saved)
        if ($this->id) {
            // Get tests data
            $testsData = TestModel::getAllRecords(match: ['requestId' => $this->id], sort: ['testName' => 'ASC']);

            foreach ($testsData as $testModel) {
                $testId = $testModel->getProperty('id');
                $testName = $testModel->getProperty('testName');
                $testType = $testModel->getProperty('testType');
                $testAssertion = $testModel->getProperty('testAssertion');

                // Run test
                $testResult = new HttpTest($client, $testType, $testAssertion);

                // Update test results
                $this->testsResults[] = [
                    'id' => $testId,
                    'name' => $testName,
                    'type' => $testType,
                    'assertion' => $testAssertion,
                    'value' => $testResult->getProperty('value'),
                    'result' => $testResult->getProperty('result'),
                ];

                // Alternative - this has the test ID as the array key
                //$this->testsResults[$testId]['id'] = $testId;
                //$this->testsResults[$testId]['name'] = $testName;
                //$this->testsResults[$testId]['type'] = $testType;
                //$this->testsResults[$testId]['assertion'] = $testAssertion;
                //$this->testsResults[$testId]['value'] = $testResult->getProperty('value');
                //$this->testsResults[$testId]['result'] = $testResult->getProperty('result');
            }

        // Set session
        Application::app()->session()->set('response/testsResults', $this->testsResults);
        }
    }

    public static function handleSession(int $requestId): bool
    {
        // Get request details
        $requestData = self::getSingleRecord(['id' => $requestId]);
        $collectionId = $requestData->getProperty('collectionId');
        $requestName = $requestData->getProperty('requestName');

        // Get collection details
        $collectionData = CollectionModel::getSingleRecord(['id' => $collectionId]);
        $collectionUserId = $collectionData->getProperty('userId');

        // Confirm user ID matches logged in user ID
        if ($collectionUserId == Application::app()->user()->id()) {
            // Remove response session data
            Application::app()->session()->remove('response');

            // Confirm body file is present
            if ($requestData->getProperty('requestBodyFile') && !file_exists($requestData->getProperty('requestBodyFile'))) {
                $requestData->setProperty('requestBodyFile', null);
            }

            // Register session variables
            Application::app()->session()->set('home/left/requestId', $requestId);
            Application::app()->session()->set('home/left/requestName', $requestName);
            Application::app()->session()->set('home/upper/requestMethod', $requestData->getProperty('requestMethod'));
            Application::app()->session()->set('home/upper/requestUrl', $requestData->getProperty('requestUrl'));
            Application::app()->session()->set('home/upper/requestParamsInputs', $requestData->getProperty('requestParamsInputs'));
            Application::app()->session()->set('home/upper/requestHeadersInputs', $requestData->getProperty('requestHeadersInputs'));
            Application::app()->session()->set('home/upper/requestAuth', $requestData->getProperty('requestAuth'));
            Application::app()->session()->set('home/upper/requestAuthBasicUsername', $requestData->getProperty('requestAuthBasicUsername'));
            Application::app()->session()->set('home/upper/requestAuthBasicPassword', $requestData->getProperty('requestAuthBasicPassword'));
            Application::app()->session()->set('home/upper/requestAuthTokenValue', $requestData->getProperty('requestAuthTokenValue'));
            Application::app()->session()->set('home/upper/requestAuthHeaderName', $requestData->getProperty('requestAuthHeaderName'));
            Application::app()->session()->set('home/upper/requestAuthHeaderValue', $requestData->getProperty('requestAuthHeaderValue'));
            Application::app()->session()->set('home/upper/requestBody', $requestData->getProperty('requestBody'));
            Application::app()->session()->set('home/upper/requestBodyTextValue', $requestData->getProperty('requestBodyTextValue'));
            Application::app()->session()->set('home/upper/requestBodyTextType', $requestData->getProperty('requestBodyTextType'));
            Application::app()->session()->set('home/upper/requestBodyFormInputs', $requestData->getProperty('requestBodyFormInputs'));
            Application::app()->session()->set('home/upper/requestBodyFileExisting', $requestData->getProperty('requestBodyFile'));
            Application::app()->session()->set('home/upper/requestVariablesInputs', $requestData->getProperty('requestVariablesInputs'));
            Application::app()->session()->set('home/upper/requestModified', false);

            // Remove request error
            Application::app()->session()->remove('tests/upper/requestError');

            // Only register the request name if it differs from the request URL
            $requestData->getProperty('requestName') != $requestData->getProperty('requestUrl') ? Application::app()->session()->set('home/upper/requestName', $requestData->getProperty('requestName')): Application::app()->session()->set('home/upper/requestName', null);

            return true;
        }

        return false;
    }

    public static function clearSession(): bool
    {
        // Remove response session data
        Application::app()->session()->remove('response');

        // Remove session variables
        Application::app()->session()->remove('home/left/requestId');
        Application::app()->session()->remove('home/left/requestName');
        Application::app()->session()->remove('home/upper/requestMethod');
        Application::app()->session()->remove('home/upper/requestUrl');
        Application::app()->session()->remove('home/upper/requestName');
        Application::app()->session()->remove('home/upper/requestParamsInputs');
        Application::app()->session()->remove('home/upper/requestHeadersInputs');
        Application::app()->session()->remove('home/upper/requestAuth');
        Application::app()->session()->remove('home/upper/requestAuthBasicUsername');
        Application::app()->session()->remove('home/upper/requestAuthBasicPassword');
        Application::app()->session()->remove('home/upper/requestAuthTokenValue');
        Application::app()->session()->remove('home/upper/requestAuthHeaderName');
        Application::app()->session()->remove('home/upper/requestAuthHeaderValue');
        Application::app()->session()->remove('home/upper/requestBody');
        Application::app()->session()->remove('home/upper/requestBodyTextValue');
        Application::app()->session()->remove('home/upper/requestBodyTextType');
        Application::app()->session()->remove('home/upper/requestBodyFormInputs');
        Application::app()->session()->remove('home/upper/requestBodyFileExisting');
        Application::app()->session()->remove('home/upper/requestVariablesInputs');
        Application::app()->session()->remove('tests/upper/requestError');

        // Register session variables
        Application::app()->session()->set('home/upper/requestModified', false);

        return true;
    }

    public function requestMethodDisplay(): string
    {
        $httpMethodList = [
            "get" => "GET",
            "head" => "HEAD",
            "post" => "POST",
            "put" => "PUT",
            "patch" => "PATCH",
            "delete" => "DEL",
            "options" => "OPT"
        ];

        return $httpMethodList[$this->requestMethod];
    }

    public static function collectionName($requestId): ?string
    {
        // Get collection ID
        $collectionId = self::getSingleRecord(['id' => $requestId])->getProperty('collectionId');

        // Check for collection ID
        if ($collectionId) {
            return CollectionModel::getSingleRecord(['id' => $collectionId])->getProperty('collectionName');
        } else {
            return null;
        }
    }
}
