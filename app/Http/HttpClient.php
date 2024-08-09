<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Application;
use App\Core\Functions;
use App\Core\Traits\GetSetProperty;
use App\Functions\Data;
use App\Models\CollectionModel;
use App\Models\RequestModel;
use App\Models\SettingsModel;
use App\Traits\LogTrait;
use ReflectionClass;
use Symfony\Component\HttpClient\CurlHttpClient;
use Throwable;

class HttpClient
{
    use GetSetProperty;
    use LogTrait;

    protected ?object $response = null;
    protected ?object $exception = null;
    protected ?string $exceptionClass = null;
    protected ?bool $responseValid = null;
    protected ?string $responseScheme = null;
    protected ?string $responseSchemeIcon = null;
    protected ?int $responseCode = null;
    protected ?string $responseType = null;
    protected ?array $responseHeaders = null;
    protected ?string $responseStatusLine = null;
    protected ?string $responseStatusProtocol = null;
    protected ?string $responseStatusCode = null;
    protected ?string $responseStatusText = null;
    protected ?float $responseBodySize = null;
    protected ?string $responseBodySizeFormatted = null;
    protected ?bool $responseBodyValid = null;
    protected ?string $responseBodyContent = null;
    protected ?array $responseBodyDecoded = null;
    protected ?int $responseTime = null;
    protected ?string $responseTimeFormatted = null;
    protected ?array $variables = null;
    protected ?string $errorMessage = null;

    public function __construct(protected RequestModel $model, protected bool $saveSession = false)
    {
        // Get collection ID
        $collectionId = $this->model->getProperty('collectionId');

        // Get variables
        $variables = Data::variables($collectionId);

        // Set properties
        $this->variables = $variables;
    }

    public function request(): void
    {
        // Time
        $clientStart = microtime(true);

        // Get status codes
        Functions::includeFile(file: '/app/Data/httpStatusCodes.php', once: true);

        // Init response for certain errors
        $response = null;

        try {
            // Get request data
            $requestMethod = $this->model->getProperty('requestMethod');
            $requestUrl = $this->model->getProperty('requestUrl');
            $requestParamsInputs = $this->model->getProperty('requestParamsInputs') ?? [];
            $requestHeadersInputs = $this->model->getProperty('requestHeadersInputs') ?? [];
            $requestAuth = $this->model->getProperty('requestAuth') ?? null;
            $requestAuthBasicUsername = $this->model->getProperty('requestAuthBasicUsername') ?? null;
            $requestAuthBasicPassword = $this->model->getProperty('requestAuthBasicPassword') ?? null;
            $requestAuthTokenValue = $this->model->getProperty('requestAuthTokenValue') ?? null;
            $requestAuthHeaderName = $this->model->getProperty('requestAuthHeaderName') ?? null;
            $requestAuthHeaderValue = $this->model->getProperty('requestAuthHeaderValue') ?? null;
            $requestBody = $this->model->getProperty('requestBody') ?? null;
            $requestBodyTextValue = $this->model->getProperty('requestBodyTextValue') ?: null; // covers empty as well
            $requestBodyTextType = $this->model->getProperty('requestBodyTextType') ?? null;
            $requestBodyFormInputs = $this->model->getProperty('requestBodyFormInputs') ?? [];
            $requestBodyFile = $this->model->getProperty('requestBodyFile');
            $requestVariablesInputs = $this->model->getProperty('requestVariablesInputs') ?? [];

            // Check for variables
            $requestUrl = $this->checkVariable($requestUrl);
            $requestAuthBasicUsername = $this->checkVariable($requestAuthBasicUsername);
            $requestAuthBasicPassword = $this->checkVariable($requestAuthBasicPassword);
            $requestAuthTokenValue = $this->checkVariable($requestAuthTokenValue);
            $requestAuthHeaderName = $this->checkVariable($requestAuthHeaderName);
            $requestAuthHeaderValue = $this->checkVariable($requestAuthHeaderValue);
            $requestBodyTextValue = $this->checkVariable($requestBodyTextValue);

            // URL - add default scheme if scheme is missing
            if (!str_contains($requestUrl, '://')) {
                $requestUrl = SettingsModel::getSetting('http/defaultScheme') . $requestUrl;
            }

            // Convert method to uppercase
            $requestMethod = strtoupper($requestMethod);

            // Convert table inputs into send data
            $requestParamsSend = $this->processInputs($requestParamsInputs);
            $requestHeadersSend = $this->processInputs($requestHeadersInputs);
            $requestBodyFormSend = $this->processInputs($requestBodyFormInputs);

            // Process the variables table
            $requestVariablesSave = $this->processVariables($requestVariablesInputs);

            // Body content types
            // https://www.iana.org/assignments/media-types/media-types.xhtml
            $requestContentTypeArray = [
                "plain" => 'text/plain',
                "json" => 'application/json',
                "html" => 'text/html',
                "xml" => 'application/xml',
                "yaml" => 'application/yaml',
                "file" => 'application/octet-stream',
            ];

            // Get settings
            $settings_http_version = SettingsModel::getSetting('http/version');
            $settings_http_timeout = SettingsModel::getSetting('http/timeout');

            // Add our User-Agent if not already present
            if (!array_key_exists('User-Agent', $requestHeadersSend)) {
                $requestHeadersSend['User-Agent'] = 'Photon';
            }

            // Add accept header from settings if not already present
            if (!array_key_exists('Accept', $requestHeadersSend)) {
                $settings_http_accept = SettingsModel::getSetting('http/accept');
                if ($settings_http_accept != 'default') $requestHeadersSend['Accept'] = $settings_http_accept;
            }

            // Init client array
            $clientArray = [];
            if ($settings_http_version != 'auto') {
                $clientArray['http_version'] = $settings_http_version;
            }

            // Init request array
            $requestArray = [];

            // Set timeout
            $requestArray['timeout'] = $settings_http_timeout;

            // Set params
            $requestArray['query'] = $requestParamsSend;

            // Set auth

            // Basic
            if ($requestAuth == 'basic') {
                $requestArray['auth_basic'] = [$requestAuthBasicUsername, $requestAuthBasicPassword];
            }
            // Token
            elseif ($requestAuth == 'token') {
                $requestArray['auth_bearer'] = $requestAuthTokenValue;
            }
            // Header
            elseif ($requestAuth == 'header') {
                $requestHeadersSend[$requestAuthHeaderName] = $requestAuthHeaderValue;
            }

            // Set body

            // For POST request without body
            if ($requestMethod == 'POST' && $requestBody == 'none') {
                // Set content type to empty
                $requestHeadersSend['Content-Type'] = '';
            }
            // Text
            elseif ($requestBody == 'text' && $requestBodyTextValue) {
                // Get content type
                $requestContentType = $requestContentTypeArray[$requestBodyTextType];

                // Set content type
                $requestHeadersSend['Content-Type'] = $requestContentType;

                // Set body
                $requestArray['body'] = $requestBodyTextValue;
            }
            // Form
            elseif ($requestBody == 'form' && count($requestBodyFormSend) > 0) {
                $requestArray['body'] = $requestBodyFormSend;
            }
            // File
            elseif ($requestBody == 'file' && $requestBodyFile !== null) {
                // Add file to body
                $requestArray['body'] = fopen($requestBodyFile, 'r');

                // Get content type
                $requestContentType = $requestContentTypeArray['file'];

                // Set content type
                $requestHeadersSend['Content-Type'] = $requestContentType;
            }

            // Set headers
            $requestArray['headers'] = $requestHeadersSend;

            // New client
            $client = new CurlHttpClient($clientArray);

            // Get the response
            $response = $client->request($requestMethod, $requestUrl, $requestArray);

            // Responses in the HTTP client are "lazy" - we need to check the headers otherwise we cannot
            // catch exceptions in this try/catch block
            $response->getHeaders(false);

            // Set response
            $this->response = $response;
            $this->responseValid = true;

            // Process info
            $this->processInfo($response);

            // Save variables
            $this->saveVariables($requestVariablesSave);
        } catch (Throwable $exception) {
            // Get exception class
            $exceptionClass = (new ReflectionClass($exception))->getShortName();

            // Set properties
            $this->exception = $exception;
            $this->exceptionClass = $exceptionClass;
            $this->errorMessage = $exception->getMessage();

            // Process the response for Client exceptions
            if ($exceptionClass == 'ClientException') {
                // Set response
                $this->response = $response;
                $this->responseValid = true;

                // Process info
                $this->processInfo($response);
            } else {
                // Set response
                $this->responseValid = false;
            }
        }

        // Calculate client time
        $clientTime = (int)round((microtime(true) - $clientStart) * 1000);

        // Set time
        $this->responseTime = $clientTime;

        // Set response time format
        if ($clientTime >= 1000) {
            $this->responseTimeFormatted = number_format($clientTime / 1000, 1) . " s";
        } else {
            $this->responseTimeFormatted = "$clientTime ms";
        }

        // Save to session if set to true
        if ($this->saveSession) $this->saveToSession();
    }

    public function processInputs(array $inputs): array
    {
        $processed = [];

        foreach ($inputs as $input) {
            $inputEnabled = $input['enabled'];
            $inputName = $input['name'];
            $inputValue = $input['value'];

            if ($inputEnabled == 'on' && $inputName != '' && $inputValue != '') {
                // Handle variables
                $inputName = $this->checkVariable($inputName);
                $inputValue = $this->checkVariable($inputValue);

                // Update array
                $processed[$inputName] = $inputValue;
            }
        }

        return $processed;
    }

    public function processVariables(array $inputs): array
    {
        $processed = [];

        foreach ($inputs as $input) {
            $inputEnabled = $input['enabled'];
            $inputKey = $input['key'];
            $inputName = $input['name'];

            if ($inputEnabled == 'on' && $inputKey != '' && $inputName != '' && !array_key_exists($inputName, $processed)) {
                $processed[$inputName] = $inputKey;
            }
        }

        return $processed;
    }

    public function processInfo(object $response): void
    {
        // Get response info
        $responseInfo = $response->getInfo();
        $responseScheme = $responseInfo['scheme'] ?: null; // handle empty string

        // Set response scheme icon
        if ($responseScheme == 'HTTPS') {
            $this->responseSchemeIcon = 'lock';
        } else {
            $this->responseSchemeIcon = 'lock-open';
        }

        // For exceptions, important to get the status code from the exception,
        // not the response, otherwise an exception will be thrown here
        // An example is the TransportException for network errors
        if ($this->exception) {
            // If response code is 0, set to null
            $this->exception->getCode() !== 0 ? $responseCode = $this->exception->getCode() : $responseCode = null;
        } else {
            // Get response code
            $responseCode = $response->getStatusCode();
        }

        // Get response type and headers
        $responseType = HTTP_DESCRIPTIONS[$responseCode] ?? null; // handle type not listed
        $responseHeaders = $responseInfo['response_headers'];

        // Get response body contents
        $responseBodyContent = $response->getContent(false);
        if ($responseBodyContent != '') {
            // Calculate body size
            $responseBodySize = round(strlen($responseBodyContent) / 1024, 2);

            // Set properties
            $this->responseBodyContent = $responseBodyContent;
            $this->responseBodySize = $responseBodySize;
            $this->responseBodySizeFormatted = number_format($responseBodySize, 2) . " KiB";;

            // Decode body
            $responseBodyDecoded = json_decode($responseBodyContent, true);

            // Check body - json_decode() will decode some responses that are not valid JSON
            // and they will not decode to a valid array, so check for array
            if (is_array($responseBodyDecoded)) {
                $this->responseBodyValid = true;
                $this->responseBodyDecoded = $responseBodyDecoded;
            } else {
                $this->responseBodyValid = false;
            }
        }

        // Set properties
        $this->responseScheme = $responseScheme;
        $this->responseCode = $responseCode;
        $this->responseType = $responseType;

        // Process headers
        $this->processHeaders($responseHeaders);
    }

    public function processHeaders(array $responseHeaders): void
    {
        // Check headers
        if (count($responseHeaders) > 0) {
            // Get status line
            $responseStatusLine = trim($responseHeaders[0]);
            if ($responseStatusLine) {

                // Get status protocol version, code and text
                $responseStatusLineArray = explode(' ', trim($responseStatusLine), 3);
                $responseStatusProtocol = $responseStatusLineArray[0] ?? null;
                $responseStatusCode = $responseStatusLineArray[1] ?? null;

                // Some sites don't report the status text
                if (array_key_exists(2, $responseStatusLineArray)) {
                    $responseStatusText = $responseStatusLineArray[2];
                } else {
                    $responseStatusText = $this->responseType;
                }

                // Set properties
                $this->responseStatusLine = $responseStatusLine;
                $this->responseStatusProtocol = $responseStatusProtocol;
                $this->responseStatusCode = $responseStatusCode;
                //$this->responseStatusText = $responseStatusText;

                // Alternative - getting response status text from HTTP_DESCRIPTIONS instead
                $this->responseStatusText = $this->responseType;
            }

            // Remove status line from headers
            array_shift($responseHeaders);

            // Create associative array
            $responseHeadersAssoc = [];
            foreach ($responseHeaders as $responseHeader) {
                $responseHeaderArray = explode(':', $responseHeader, 2);
                $responseHeaderName = $responseHeaderArray[0] ?? null;
                $responseHeaderValue = $responseHeaderArray[1] ?? null;;

                if ($responseHeaderName && $responseHeaderValue) {
                    $responseHeaderName = trim($responseHeaderName);
                    $responseHeaderValue = trim($responseHeaderValue);
                    $responseHeadersAssoc[$responseHeaderName] = $responseHeaderValue;
                }
            }

            // Set properties
            $this->responseHeaders = $responseHeadersAssoc;
        }
    }

    public function saveVariables(array $requestVariables): void
    {
        if ($this->responseBodyValid) {
            // Check for array based response body
            // If response body is array, we will use the first record
            if (array_is_list($this->responseBodyDecoded)) {
                $bodyDecoded =  $this->responseBodyDecoded[0];
            } else {
                $bodyDecoded =  $this->responseBodyDecoded;
            }

            // Process body
            foreach($bodyDecoded as $key => $value) {
                if (in_array($key, $requestVariables)) {
                    foreach ($requestVariables as $variableName => $variableKey) {
                        if ($variableKey == $key) {
                            // Cast value to string
                            settype($value, 'string');

                            // Get collection ID
                            $collectionId = $this->model->getProperty('collectionId');

                            // Debug
                            $this->log("http", ["Set: " . $variableName . " | value: " . $value]);

                            // Save to session
                            Application::app()->session()->set("variables/$collectionId/$variableName", ['value' => $value, 'type' => 'request', 'timestamp' => time()]);
                        }
                    }
                }
            }
        }
    }

    public function checkVariable(mixed $value): mixed
    {
        // Check for {{placeholder}} in the value
        if ($value && preg_match('/{{(.*?)}}/', $value)) {
            foreach ($this->variables as $variableName => $variableArray) {
                $variableValue = $variableArray['value'];
                $value = str_replace('{{' . $variableName . '}}', $variableValue, $value);
            }
        }

        return $value;
    }

    public function saveToSession()
    {
        Application::app()->session()->set('response/responseRequestTime', time());
        Application::app()->session()->set('response/responseValid', $this->responseValid);
        Application::app()->session()->set('response/responseException', (bool)$this->exception);
        Application::app()->session()->set('response/responseExceptionClass', $this->exceptionClass);
        Application::app()->session()->set('response/responseScheme', $this->responseScheme);
        Application::app()->session()->set('response/responseSchemeIcon', $this->responseSchemeIcon);
        Application::app()->session()->set('response/responseCode', $this->responseCode);
        Application::app()->session()->set('response/responseType', $this->responseType);
        Application::app()->session()->set('response/responseHeaders', $this->responseHeaders);
        Application::app()->session()->set('response/responseStatusLine', $this->responseStatusLine);
        Application::app()->session()->set('response/responseStatusProtocol', $this->responseStatusProtocol);
        Application::app()->session()->set('response/responseStatusCode', $this->responseStatusCode);
        Application::app()->session()->set('response/responseStatusText', $this->responseStatusText);
        Application::app()->session()->set('response/responseBodyContent', $this->responseBodyContent);
        Application::app()->session()->set('response/responseBodyDecoded', $this->responseBodyDecoded);
        Application::app()->session()->set('response/responseBodySize', $this->responseBodySize);
        Application::app()->session()->set('response/responseBodySizeFormatted', $this->responseBodySizeFormatted);
        Application::app()->session()->set('response/responseBodyValid', $this->responseBodyValid);
        Application::app()->session()->set('response/responseTime', $this->responseTime);
        Application::app()->session()->set('response/responseTimeFormatted', $this->responseTimeFormatted);
        Application::app()->session()->set('response/responseErrorMessage', $this->errorMessage);
    }
}
