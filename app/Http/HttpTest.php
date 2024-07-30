<?php

namespace App\Http;

use App\Core\Application;
use App\Core\Functions;
use App\Core\Traits\GetSetProperty;
use App\Functions\Data;
use App\Models\CollectionModel;
use App\Models\SettingsModel;

class HttpTest
{
    use GetSetProperty;

    private mixed $value = "{{init_value}}"; // allows us to distinguish from actual null values and keys that are not present
    private ?string $result = null;
    private ?array $headers = null;
    private ?array $bodyDecoded = null;
    private ?array $bodyDecodedFirst = null;
    private ?int $records = null;
    private ?string $assertionKey = null;
    private mixed $assertionValue = null;

    public function __construct(private HttpClient $client, private string $type, private string $assertion)
    {
        // Get collection ID
        $collectionId = $this->client->getProperty('model')->getProperty('collectionId');

        // Get variables
        $variables = Data::variables($collectionId);

        // Variables - check for {{X}} placeholder in the assertion
        if ($this->assertion && preg_match('/{{(.*?)}}/', $this->assertion)) {
            foreach ($variables as $variableName => $variableArray) {
                $variableValue = $variableArray['value'];
                $this->assertion = str_replace('{{' . $variableName . '}}', $variableValue, $this->assertion);
            }
        }

        // Check for key-value pair in the assertion
        if (str_contains($this->assertion, '||')) {
            $assertionPair = explode('||', $this->assertion, 2);
            $this->assertionKey = trim($assertionPair[0]);
            $this->assertionValue = trim($assertionPair[1]);
        }

        // Check for [[space]] placeholder in the assertion
        if ($this->assertion && str_contains($this->assertion, '[[space]]')) {
            $this->assertion = str_replace('[[space]]', ' ', $this->assertion);
        }
        if ($this->assertionValue && str_contains($this->assertionValue, '[[space]]')) {
            $this->assertionValue = str_replace('[[space]]', ' ', $this->assertionValue);
        }

        // Get headers and set property with keys changed to lowercase
        $headers = $this->client->getProperty('responseHeaders');
        $this->headers = array_change_key_case($headers);

        // Get decoded body and set property
        $this->bodyDecoded = $this->client->getProperty('responseBodyDecoded');

        // Check if decoded body is a list
        if ($this->bodyDecoded && array_is_list($this->bodyDecoded)) {
            $this->bodyDecodedFirst = $this->bodyDecoded[0];
            $this->records = count($this->bodyDecoded);
        } elseif ($this->bodyDecoded) {
            $this->bodyDecodedFirst = $this->bodyDecoded;
            $this->records = 1;
        }

        // Run test
        $this->runTest();
    }

    public function runTest(): void
    {
        // Determine test method name
        $testMethod = Functions::dotToCamel($this->type);

        if (method_exists($this, $testMethod) ) {
            // Run test
            if ($this->$testMethod()) {
                $this->result = 'passed';
            } else {
                $this->result = 'failed';
            }
        } else {
            // Cannot determine test, test has been skipped
            $this->result = 'skipped';
        }

        // Need to encode value to show "true/false" and "null" values as strings in the interface
        // If the value has not changed since init, set to null
        // Don't encode if already a string and check for not null
        if ($this->value === '{{init_value}}') {
            $this->value = null;
        } elseif (!is_string($this->value)) {
            $this->value = json_encode($this->value, JSON_UNESCAPED_SLASHES);
        }
    }

    private function responseValid(): bool
    {
        $this->value = json_encode($this->client->getProperty('responseValid'));

        if ($this->value == $this->assertion) {
            return true;
        }

        return false;
    }

    private function responseCode(): bool
    {
        $this->value = $this->client->getProperty('responseCode');

        if ($this->value == $this->assertion) {
            return true;
        }

        return false;
    }

    private function responseScheme(): bool
    {
        $this->value = $this->client->getProperty('responseScheme');

        if (strtolower($this->value) == strtolower($this->assertion)) {
            return true;
        }

        return false;
    }

    private function responseTimeLessThan(): bool
    {
        $this->value = $this->client->getProperty('responseTime');

        if ($this->value < $this->assertion) {
            return true;
        }

        return false;
    }

    private function responseTimeGreaterThan(): bool
    {
        $this->value = $this->client->getProperty('responseTime');

        if ($this->value > $this->assertion) {
            return true;
        }

        return false;
    }

    private function headersCountEquals(): bool
    {
        $this->value = count($this->client->getProperty('responseHeaders'));

        if ($this->value == $this->assertion) {
            return true;
        }

        return false;
    }

    private function headersHeaderPresent(): bool
    {
        if (array_key_exists(strtolower($this->assertion), $this->headers)) {
            $this->value = 'present';
            return true;
        }

        $this->value = 'not present';
        return false;
    }

    private function headersHeaderEquals(): bool
    {
        if ($this->assertionKey && $this->assertionValue && array_key_exists(strtolower($this->assertionKey), $this->headers)) {
            $this->value = $this->headers[strtolower($this->assertionKey)];

            if ($this->value == $this->assertionValue) {
                return true;
            }
        }

        return false;
    }

    private function headersHeaderContains(): bool
    {
        if ($this->assertionKey && $this->assertionValue && array_key_exists(strtolower($this->assertionKey), $this->headers)) {
            $this->value = $this->headers[strtolower($this->assertionKey)];

            if (str_contains($this->value, $this->assertionValue)) {
                return true;
            }
        }

        return false;
    }

    private function jsonValid(): bool
    {
        $this->value = json_encode($this->client->getProperty('responseBodyValid'));

        if ($this->value == $this->assertion) {
            return true;
        }

        return false;
    }

    private function jsonKeyPresent(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = 'present';
            return true;
        }

        $this->value = 'not present';
        return false;
    }

    private function jsonKeyNotPresent(): bool
    {
        return !$this->jsonKeyPresent();
    }

    private function jsonKeyEquals(): bool
    {
        if ($this->bodyDecodedFirst && $this->assertionKey && $this->assertionValue && array_key_exists($this->assertionKey, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertionKey];

            $type = gettype($this->value);

            // Check for boolean non-true and null
            if ($type == 'boolean' && $this->assertionValue != 'true') $this->assertionValue = null;
            if ($this->assertionValue == 'null') $this->assertionValue = null;

            // Cast assertion value type to the key value type if type is not null
            if ($type != 'NULL') settype($this->assertionValue, $type);

            if ($this->value === $this->assertionValue) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyNotEquals(): bool
    {
        if ($this->bodyDecodedFirst && $this->assertionKey && $this->assertionValue && array_key_exists($this->assertionKey, $this->bodyDecodedFirst)) {
            return !$this->jsonKeyEquals();
        }

        return false;
    }

    private function jsonKeyContains(): bool
    {
        if ($this->bodyDecodedFirst && $this->assertionKey && $this->assertionValue && array_key_exists($this->assertionKey, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertionKey];

            if (str_contains($this->value, $this->assertionValue)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyNotContains(): bool
    {
        if ($this->bodyDecodedFirst && $this->assertionKey && $this->assertionValue && array_key_exists($this->assertionKey, $this->bodyDecodedFirst)) {
            return !$this->jsonKeyContains();
        }

        return false;
    }

    private function jsonKeyIsString(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_string($this->value)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsNumber(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_numeric($this->value)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsInteger(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_int($this->value)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsFloat(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_float($this->value)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsBoolean(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_bool($this->value)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsNull(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_null($this->value)) {
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsArray(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_array($this->value) && array_is_list($this->value)) {
                $this->value = 'array';
                return true;
            }
        }

        return false;
    }

    private function jsonKeyIsObject(): bool
    {
        if ($this->bodyDecodedFirst && array_key_exists($this->assertion, $this->bodyDecodedFirst)) {
            $this->value = $this->bodyDecodedFirst[$this->assertion];

            if (is_array($this->value) && !array_is_list($this->value)) {
                $this->value = 'object';
                return true;
            }
        }

        return false;
    }

    private function jsonRecordsCountEquals(): bool
    {
        if ($this->bodyDecoded) {
            $this->value = $this->records;

            if ($this->value == $this->assertion) {
                return true;
            }
        }

        return false;
    }

    private function jsonRecordsCountLessThan(): bool
    {
        if ($this->bodyDecoded) {
            $this->value = $this->records;

            if ($this->value < $this->assertion) {
                return true;
            }
        }

        return false;
    }

    private function jsonRecordsCountGreaterThan(): bool
    {
        if ($this->bodyDecoded) {
            $this->value = $this->records;

            if ($this->value > $this->assertion) {
                return true;
            }
        }

        return false;
    }
}
