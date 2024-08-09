<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Application;
use App\Core\Functions;
use App\Core\Session;
use App\Models\SettingsModel;
use App\Traits\LogTrait;

Functions::includeFile(file: '/app/Data/ajax.php');

class AjaxRequest
{
    use LogTrait;

    private Session $session;
    private ?string $token;
    private ?string $key;
    private mixed $value;
    private bool $process;
    private ?string $file;
    private ?array $variables;
    private ?string $class;
    private ?string $method;
    private ?array $classParameters;
    private ?array $methodParameters;
    private const array KEYS_ALLOWED = KEYS_ALLOWED;
    private const array FILES_ALLOWED = FILES_ALLOWED;
    private const array METHODS_ALLOWED = METHODS_ALLOWED;
    private const array RECORD_KEYS = RECORD_KEYS;
    private const array SETTINGS_UPDATE = SETTINGS_UPDATE;

    public function __construct()
    {
        // Create session
        $this->session = new Session('ajax');

        // Get token
        $token = $_POST['token'] ?: null;
        $this->token = $token;

        // Confirm token is valid
        if ($this->token !== session_id()) {
            // Set status code and exit
            http_response_code(401);
            exit;
        }

        // Get post values
        $key = $_POST['key'] ?? null;
        $value = $_POST['value'] ?? null;
        $process = $_POST['process'] ?? false;
        $file = $_POST['file'] ?? null;
        $variables = $_POST['variables'] ?? null;
        $class = $_POST['class'] ?? null;
        $method = $_POST['method'] ?? null;
        $classParameters = $_POST['classParameters'] ?? null;
        $methodParameters = $_POST['methodParameters'] ?? null;

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
        // Debug
        $this->log("ajax", [
            "from: handlePost()",
            " key: " . $this->key . " | process: " . json_encode($this->process) . " | type: " . gettype($this->value),
            " tkn: " . $this->token . " | session: " . session_id()
        ]);

        // Restrict to allowed keys
        if (!array_key_exists($this->key, $this::KEYS_ALLOWED)) {
            // Set status code and exit
            http_response_code(403);
            exit;
        }

        // Check if user is logged in - get user ID
        // Set user ID to 0 if not logged in - this is the guest user
        $userId = $this->session->get('user/id') ?? 0;

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
            Functions::deleteFile($this->value);
        }

        // Check key for clear session variable
        elseif ($this->key === 'variables/clear') {
            $this->session->remove('variables/' . $this->value);
        }

        // Check if processing the value into individual keys/values
        elseif (is_array($this->value) && $this->process) {
            foreach ($this->value as $subKey => $subValue) {
                // Set key
                $this->session->set("$this->key/$subKey", $subValue);
            }
        }

        else {
            // Set key
            $this->session->set($this->key, $this->value);
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
        $recordModifiedKey = $this::KEYS_ALLOWED[$this->key];
        if ($recordModifiedKey) {
            // Get the session key for the relevant selected record ID
            $recordIdKey = $this::RECORD_KEYS[$recordModifiedKey];

            // Get the record ID currently selected
            $recordIdValue = $this->session->get($recordIdKey);

            // Debug
            //$this->log("ajax", ["Session: " . $recordModifiedKey . " | key: " . $recordIdKey . " | ID: " . $recordIdValue]);

            // Set modified key if we have a record ID
            if ($recordIdValue) $this->session->set($recordModifiedKey, true);
        }

        // Check if we need to save to user setting
        // Only if user ID is valid and key is present in the settings update array
        if ($userId && in_array($this->key, $this::SETTINGS_UPDATE)) {
            // Create application instance - we need this for database access
            new Application();

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
        if (!in_array($this->file, $this::FILES_ALLOWED)) {
            // Set status code and exit
            http_response_code(403);
            exit;
        }

        // Create application instance - we need this for session and database access
        new Application();

        // Load file
        Functions::includeFile(file: $this->file, variables: $this->variables);
    }

    public function handleMethod(): void
    {
        // Restrict to allowed class/methods
        if (!in_array($this->class . '::' . $this->method . "()", $this::METHODS_ALLOWED)) {
            // Set status code and exit
            http_response_code(403);
            exit;
        }

        // Confirm method exists
        if (method_exists($this->class, $this->method)) {
            // Create application instance - we need this for session and database access
            new Application();

            // Create object
            $class = new $this->class($this->classParameters);

            // Run method
            $method = $this->method;
            $class->$method($this->methodParameters);
        }
    }
}
