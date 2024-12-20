<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Session;
use App\Models\SettingsModel;

includeFile(file: '/app/Data/ajax.php');

class AjaxRequest
{
    private Session $session;
    private ?string $token;
    private ?string $key;
    private string|array|null $value;
    private ?bool $process;
    private ?string $file;
    private ?array $variables;
    private ?string $class;
    private ?string $method;
    private ?array $classParameters;
    private ?array $methodParameters;

    public function __construct()
    {
        // Get post data
        $postData = request()->getBody(false);

        // Get token
        $this->token = $postData['token'] ?? null;

        // Confirm token matches the users token
        if ($this->token !== session()->get('user/token')) {
            // Set status code and exit
            http_response_code(401);
            exit;
        }

        // Get post values
        $key = $postData['key'] ?? null;
        $value = $postData['value'] ?? null;
        $process = $postData['process'] ?? false;
        $file = $postData['file'] ?? null;
        $variables = $postData['variables'] ?? null;
        $class = $postData['class'] ?? null;
        $method = $postData['method'] ?? null;
        $classParameters = $postData['classParameters'] ?? null;
        $methodParameters = $postData['methodParameters'] ?? null;

        // Cast process to bool
        settype($process, 'bool');

        // Session/settings update
        if ($key) {
            // Check for empty array
            if ($value === '{{emptyArray}}') $value = [];

            // Set properties
            $this->key = $key;
            $this->value = $value;
            $this->process = $process;

            // Handle post
            $this->handlePost();
        }

        // File load
        if ($file) {
            // Set properties
            $this->file = $file;
            $this->variables = $variables;

            // Handle file
            $this->handleFile();
        }

        // Method in class
        if ($class) {
            // Set properties
            $this->class = $class;
            $this->method = $method;
            $this->classParameters = $classParameters;
            $this->methodParameters = $methodParameters;

            // Handle method
            $this->handleMethod();
        }
    }

    public function handlePost(): void
    {
        // Debug log
        logger()->logDebug('ajax.log', [
            "from: handlePost()",
            " key: " . $this->key . " | process: " . json_encode($this->process) . " | type: " . gettype($this->value),
            " tkn: " . $this->token . " | session: " . session_id(),
        ]);

        // Restrict to allowed keys
        if (!array_key_exists($this->key, KEYS_ALLOWED)) {
            // Set status code and exit
            http_response_code(403);
            exit;
        }

        // Check if user is logged in - get user ID
        // Set user ID to 0 if not logged in - this is the guest user
        $userId = session()->get('user/id');

        // Check key for re-ordering the request list
        if ($this->key === 'home/upper/requestsList') {
            // Get collection ID and records
            $collectionId = $this->value['collection'];
            $collectionRecords = $this->value['records'];

            // Init sort number
            $requestSort = 1;

            // Process records
            foreach ($collectionRecords as $requestId) {
                // Update request
                $sql = "UPDATE requests SET sort_order = $requestSort WHERE id = $requestId";
                db()->query($sql);

                // Increment sort number
                $requestSort++;
            }
        }

        // Check key for file delete request
        if ($this->key === 'home/upper/requestBodyFileDelete') {
            // Set uploaded body files directory
            $uploadedFilesDirectory = UPLOAD_PATH . "/$userId";

            // Get file directory
            $uploadedFileDirectory = dirname($this->value);

            // Restrict path to uploads directory only
            if ($uploadedFileDirectory != $uploadedFilesDirectory) {
                // Set status code and exit
                http_response_code(403);
                exit;
            }

            // Delete file
            deleteFile($this->value);
        }

        // Check key for clear session variable
        elseif ($this->key === 'variables/clear') {
            session()->remove('variables/' . $this->value);
        }

        // Check if processing the value into individual keys/values
        elseif (is_array($this->value) && $this->process) {
            foreach ($this->value as $subKey => $subValue) {
                // Set key
                session()->set("$this->key/$subKey", $subValue);
            }
        }

        else {
            // Set key
            session()->set($this->key, $this->value);
        }

        // Debug
        //echo "\nReceived:\n";
        //echo "token: " . $this->token . "\n";
        //echo "user: $userId\n";
        //echo "key: " . $this->key . "\n";
        //if (is_array($this->value)) {
        //    echo "value:\n";
        //    print_r($this->value);
        //} else {
        //    echo "value: " . $this->value . "\n";
        //}
        //if ($this->value === null) {
        //    echo "value is null\n";
        //}

        // Indicate to the user that changes have been made to the record
        $recordModifiedKey = KEYS_ALLOWED[$this->key];
        if ($recordModifiedKey) {
            // Get the session key for the relevant selected record ID
            $recordIdKey = RECORD_KEYS[$recordModifiedKey];

            // Get the record ID currently selected
            $recordIdValue = session()->get($recordIdKey);

            // Debug log
            //logger()->logDebug('ajax.log', ["Session: " . $recordModifiedKey . " | key: " . $recordIdKey . " | ID: " . $recordIdValue]);

            // Set modified key if we have a record ID
            if ($recordIdValue) session()->set($recordModifiedKey, true);
            if ($recordIdValue) session()->set($recordModifiedKey, true);
        }

        // Check if we need to save to user setting
        // Only if user ID is valid and key is present in the settings update array
        if ($userId && in_array($this->key, SETTINGS_UPDATE)) {
            // Check if processing the value into individual keys/values
            if (is_array($this->value) && $this->process) {
                foreach ($this->value as $subKey => $subValue) {
                    // Update setting
                    SettingsModel::updateSetting($this->key . "/$subKey", $subValue);
                }
            } else {
                // Update setting
                SettingsModel::updateSetting($this->key, $this->value);
            }
        }
    }

    public function handleFile(): void
    {
        // Restrict to allowed files
        if (!in_array($this->file, FILES_ALLOWED)) {
            // Set status code and exit
            http_response_code(403);
            exit;
        }

        // Load file
        includeFile(file: $this->file, variables: $this->variables);
    }

    public function handleMethod(): void
    {
        // Restrict to allowed class/methods
        if (!in_array($this->class . '::' . $this->method . "()", METHODS_ALLOWED)) {
            // Set status code and exit
            http_response_code(403);
            exit;
        }

        // Confirm method exists
        if (method_exists($this->class, $this->method)) {
            // Create object
            $class = new $this->class($this->classParameters);

            // Run method
            $method = $this->method;
            $class->$method($this->methodParameters);
        }
    }
}
