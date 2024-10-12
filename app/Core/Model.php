<?php
declare(strict_types=1);

namespace App\Core;

use App\Core\Database\Connection;
use App\Core\Traits\GetSetProperty;
use ReflectionClass;
use Throwable;

abstract class Model
{
    use GetSetProperty;

    protected const string RULE_REQUIRED = "required";
    protected const string RULE_INTEGER = "integer";
    protected const string RULE_DECIMAL = "decimal";
    protected const string RULE_EMAIL = "email";
    protected const string RULE_URL = "url";
    protected const string RULE_JSON = "json";
    protected const string RULE_MIN_LENGTH = "min length";
    protected const string RULE_MAX_LENGTH = "max length";
    protected const string RULE_MIN_VALUE = "min value";
    protected const string RULE_MAX_VALUE = "max value";
    protected const string RULE_MATCH = "match";
    protected const string RULE_UNIQUE = "unique";
    protected const string RULE_USER_EXISTS = "user exists";
    protected const string RULE_USER_ACTIVE = "user active";
    protected const string RULE_PASSWORD_VERIFY = "verify password";
    protected array $errors = [];

    abstract static public function tableName(): string;

    abstract static public function primaryKey(): array;

    abstract static public function fields(): array;

    abstract public function fieldLabels(): array;

    abstract public function rules(): array;

    public function getFieldLabel($fieldName)
    {
        return $this->fieldLabels()[$fieldName] ?? $fieldName;
    }

    public function loadData($data): void
    {
        foreach ($data as $key => $value) {
            // Only handle form elements backed by a property
            // Only handle form elements which are not an empty string
            if (property_exists($this, $key) && $value != '') {
                // Cast variable to property type
                $propertyType = (new ReflectionClass($this))->getProperty($key)->getType()->getName();
                settype($value, $propertyType);

                // Set property
                $this->$key = $value;
            }
        }
    }

    public function validate(): bool
    {
        // Get table name
        $tableName = static::tableName();

        // Get primary key
        $primaryKey = static::primaryKey();
        $primaryKeyProperty = $primaryKey['property'];
        $primaryKeyColumn = $primaryKey['column'];

        // Get model fields
        $fields = static::fields();

        // Add primary key
        $fields[$primaryKeyProperty] = $primaryKeyColumn;

        // Process rules
        foreach ($this->rules() as $fieldName => $rules) {
            // Get field value
            $fieldValue = $this->$fieldName;

            // Loop rules
            foreach ($rules as $rule) {
                if (is_array($rule)) {
                    $ruleName = $rule[0];
                } else {
                    $ruleName = $rule;
                }

                // Get rule attributes
                $ruleAttributes = $rule['attributes'] ?? [];

                if ($ruleName === self::RULE_REQUIRED && ($fieldValue === '' || $fieldValue === null)) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_REQUIRED && is_array($fieldValue) && count($fieldValue) === 0) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_INTEGER && filter_var($fieldValue, FILTER_VALIDATE_INT) === false) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_DECIMAL && filter_var($fieldValue, FILTER_VALIDATE_FLOAT) === false) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_EMAIL && filter_var($fieldValue, FILTER_VALIDATE_EMAIL) === false) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_URL && filter_var($fieldValue, FILTER_VALIDATE_URL) === false) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_JSON && json_decode($fieldValue) === null) {
                    $this->addErrorForRule($fieldName, $ruleName);
                    break;
                } elseif ($ruleName === self::RULE_MIN_LENGTH && strlen($fieldValue) < $rule["min"]) {
                    $this->addErrorForRule($fieldName, $ruleName, ['min' => $rule['min']]);
                    break;
                } elseif ($ruleName === self::RULE_MAX_LENGTH && strlen($fieldValue) > $rule["max"]) {
                    $this->addErrorForRule($fieldName, $ruleName, ['max' => $rule['max']]);
                    break;
                } elseif ($ruleName === self::RULE_MIN_VALUE && $fieldValue < $rule["min"]) {
                    $this->addErrorForRule($fieldName, $ruleName, ['min' => $rule['min']]);
                    break;
                } elseif ($ruleName === self::RULE_MAX_VALUE && $fieldValue > $rule["max"]) {
                    $this->addErrorForRule($fieldName, $ruleName, ['max' => $rule['max']]);
                    break;
                } elseif ($ruleName === self::RULE_MATCH && $fieldValue !== $this->{$rule["match"]}) {
                    // Update the match value to be the name of the form input we need to match
                    $rule['match'] = $this->getFieldLabel($rule['match']);
                    $this->addErrorForRule($fieldName, $ruleName, ['match' => $rule['match']]);
                    break;
                } elseif ($ruleName === self::RULE_UNIQUE) {
                    // Build where part of SQL
                    $sqlWhere = [];
                    foreach ($ruleAttributes as $sqlField => $sqlOperator) {
                        // Use lowercase if the type is string
                        if (gettype($this->$sqlField) == 'string') {
                            $sqlWhere[] = "LOWER(" . $fields[$sqlField] . ") $sqlOperator :$sqlField";
                        } else {
                            $sqlWhere[] = $fields[$sqlField] . " $sqlOperator :$sqlField";
                        }
                    }
                    $sqlWhere = implode(' AND ', $sqlWhere);

                    // Build SQL
                    $sql = "SELECT * FROM $tableName WHERE $sqlWhere";

                    try {
                        // Run statement
                        $statement = Application::app()->db()->prepare($sql);
                        foreach ($ruleAttributes as $sqlField => $sqlOperator) {
                            // Use lowercase if the type is string
                            if (gettype($this->$sqlField) == 'string') {
                                $statement->bindValue(":$sqlField", strtolower($this->$sqlField));
                            } else {
                                $statement->bindValue(":$sqlField", $this->$sqlField);
                            }
                        }
                        $statement->execute();
                        $data = $statement->fetchObject();
                    } catch (Throwable $exception) {
                        throw new (Functions::getConfig("class/exception/framework"))(message: "Validate failure: RULE_UNIQUE", previous: $exception);
                    }

                    // Check for data
                    if ($data) {
                        $fieldLabel = $this->getFieldLabel($fieldName);
                        $this->addErrorForRule($fieldName, $ruleName, ['unique' => $fieldLabel]);
                        break;
                    }
                } elseif ($ruleName === self::RULE_USER_EXISTS) {
                    // Build SQL
                    $sql = "SELECT * FROM $tableName WHERE " . $fields[$ruleAttributes['where']] . " = :" . $ruleAttributes['where'];

                    try {
                        // Run statement
                        $statement = Application::app()->db()->prepare($sql);
                        $statement->bindValue(":" . $ruleAttributes['where'], $fieldValue);
                        $statement->execute();
                        $data = $statement->fetchObject();
                    } catch (Throwable $exception) {
                        throw new (Functions::getConfig("class/exception/framework"))(message: "Validate failure: RULE_USER_EXISTS", previous: $exception);
                    }

                    // Check for data
                    if (!$data) {
                        $this->addErrorForRule($fieldName, $ruleName);
                        break;
                    }
                } elseif ($ruleName === self::RULE_USER_ACTIVE) {
                    // Build SQL
                    $sql = "SELECT " . $fields[$ruleAttributes['select']] . " FROM $tableName WHERE " . $fields[$ruleAttributes['where']] . " = :" . $ruleAttributes['where'];

                    try {
                        // Run statement
                        $statement = Application::app()->db()->prepare($sql);
                        $statement->bindValue(":" . $ruleAttributes['where'], $fieldValue);
                        $statement->execute();
                        $status = $statement->fetchColumn();
                    } catch (Throwable $exception) {
                        throw new (Functions::getConfig("class/exception/framework"))(message: "Validate failure: RULE_USER_ACTIVE", previous: $exception);
                    }

                    // Anything that does not match the required value is an account that is not active
                    if ($status !== $ruleAttributes['value']) {
                        $this->addErrorForRule($fieldName, $ruleName);
                        break;
                    }
                } elseif ($ruleName === self::RULE_PASSWORD_VERIFY) {
                    // Check if user exists first
                    $userEmailField = $ruleAttributes['emailField'];
                    $userPasswordField = $ruleAttributes['passwordField'];
                    $userEmailValue = $this->$userEmailField;

                    try {
                        $sql = "SELECT " . $fields[$userPasswordField] . " FROM $tableName WHERE " . $fields[$userEmailField] . " = :$userEmailField";
                        $statement = Application::app()->db()->prepare($sql);
                        $statement->bindValue(":$userEmailField", $userEmailValue);
                        $statement->execute();
                        $userPasswordValue = $statement->fetchColumn();
                    } catch (Throwable $exception) {
                        throw new (Functions::getConfig("class/exception/framework"))(message: "Validate failure: RULE_PASSWORD_VERIFY", previous: $exception);
                    }

                    // Check password
                    if ($userPasswordValue && !password_verify($fieldValue, $userPasswordValue)) {
                        $this->addErrorForRule($fieldName, $ruleName);
                        break;
                    }
                }
            }
        }

        // Check errors
        if (count($this->errors) > 0) {
            return false;
        } else {
            return true;
        }
    }

    private function addErrorForRule(string $field, string $rule, $params = []): void
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            settype($value, 'string');
            $message = str_replace(search: "{{$key}}", replace: $value, subject: $message);
        }
        $this->errors[$field] = $message;
    }

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => "This field is required",
            self::RULE_INTEGER => "Must be a valid integer",
            self::RULE_DECIMAL => "Must be a valid decimal",
            self::RULE_EMAIL => "Must be a valid e-mail address",
            self::RULE_URL => "Must be a valid URL",
            self::RULE_JSON => "Must be valid JSON",
            self::RULE_MIN_LENGTH => "Min length is {min}",
            self::RULE_MAX_LENGTH => "Max length is {max}",
            self::RULE_MIN_VALUE => "Min value is {min}",
            self::RULE_MAX_VALUE => "Max value is {max}",
            self::RULE_MATCH => "Does not match {match}",
            self::RULE_UNIQUE => "Record with {unique} already exists",
            self::RULE_USER_EXISTS => "Account does not exist",
            self::RULE_USER_ACTIVE => "Account is not activated",
            self::RULE_PASSWORD_VERIFY => "Password is incorrect",
        ];
    }

    public function hasErrors(): bool
    {
        // Check errors
        if (count($this->errors) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getError($field): ?string
    {
        // Return error message
        return $this->errors[$field] ?? null;
    }

    public static function getAllRecords(array $match = null, array $sort = null): array
    {
        // Get called class name
        $className = get_called_class();

        // Throw exception if running this method through this class
        if ($className == 'App\Core\Model') {
            throw new (Functions::getConfig("class/exception/framework"))(message: "getAllRecords() cannot be accessed through Model");
        }

        // Get table name
        $tableName = $className::tableName();

        // Get primary key
        $primaryKey = static::primaryKey();
        $primaryKeyProperty = $primaryKey['property'];
        $primaryKeyColumn = $primaryKey['column'];

        // Get model fields
        $fields = static::fields();

        // Add primary key, createdAt and updatedAt fields
        $fields[$primaryKeyProperty] = $primaryKeyColumn;
        $fields['createdAt'] = 'created_at';
        $fields['updatedAt'] = 'updated_at';

        // Create flipped fields array
        $fieldsFlipped = array_flip($fields);

        // Check for sort
        if ($sort) {
            $orderBy = ' ORDER BY ' . implode(', ', array_map(fn($a, $b) => $fields[$a] . " $b", array_keys($sort), $sort));
        } else {
            $orderBy = null;
        }

        // Check for match
        if ($match) {
            // Build WHERE part of SQL
            $sqlWhere = implode(' AND ', array_map(fn($a) => $fields[$a] . " = :$a", array_keys($match)));

            // Build SQL
            $sql = "SELECT * FROM $tableName WHERE {$sqlWhere}{$orderBy}";

            // Run statement
            $statement = Application::app()->db()->prepare($sql);
            foreach ($match as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
            $statement->execute();
        } else {
            // All records

            // Build SQL
            $sql = "SELECT * FROM {$tableName}{$orderBy}";

            // Run statement
            $statement = Application::app()->db()->prepare($sql);
            $statement->execute();
        }

        // Get data
        $data = $statement->fetchAll(Connection::FETCH_ASSOC);

        // Declare return array
        $objects = [];

        // Process records
        foreach ($data as $dataRecord) {
            // Create object
            $object = new $className();

            // Process record columns
            foreach ($dataRecord as $dataRecordKey => $dataRecordValue) {
                // Get property name
                $propertyName = $fieldsFlipped[$dataRecordKey];

                // Get property type
                $propertyType = (new ReflectionClass($object))->getProperty($propertyName)->getType()->getName();

                // If array, unserialize the string
                if ($propertyType == 'array' && $dataRecordValue !== null) {
                    $dataRecordValue = unserialize($dataRecordValue);
                }

                // Assign to object property
                $object->setProperty($propertyName, $dataRecordValue);
            }

            // Add object to return array - using record ID as array key
            $objects[$dataRecord['id']] = $object;
        }

        // Return
        return $objects;
    }

    public static function getSingleRecord(array $match): object
    {
        // Get called class name
        $className = get_called_class();

        // Throw exception if running this method through this class
        if ($className == 'App\Core\Model') {
            throw new (Functions::getConfig("class/exception/framework"))(message: "getSingleRecord() cannot be accessed through Model");
        }

        // Get record
        $dataRecord = self::getAllRecords($match);

        // Check for record
        if ($dataRecord) {
            // Return first array element
            return $dataRecord[array_key_first($dataRecord)];
        } else {
            // Return new object
            return new $className();
        }
    }

    public function insertRecord(): bool
    {
        // Get table name
        $tableName = static::tableName();

        // Get model fields
        $fields = static::fields();

        // Add createdAt and updatedAt fields
        $fields['createdAt'] = 'created_at';
        $fields['updatedAt'] = 'updated_at';

        // Columns and placedholders
        $columns = array_values($fields);
        $placeholders = array_map(fn($a) => ":$a", array_keys($fields));

        // Define query
        $sql = "INSERT INTO $tableName (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";

        // Prepare query
        $statement = Application::app()->db()->prepare($sql);

        // Bind values
        foreach (array_keys($fields) as $fieldName) {
            // Check for array value
            if (is_array($this->{$fieldName})) {
                $statement->bindValue(":$fieldName", serialize($this->{$fieldName}));
            } else {
                $statement->bindValue(":$fieldName", $this->{$fieldName});
            }
        }

        // Bind current time to the createdAt and updatedAt fields
        $insertedAt = date('Y-m-d H:i:s');
        $statement->bindValue(":createdAt", $insertedAt);
        $statement->bindValue(":updatedAt", $insertedAt);

        // Run query
        $statement->execute();

        // Get record ID
        $id = (int)Application::app()->db()->lastInsertId();

        // Update the model that called this method
        $this->id = $id;

        // Return
        return true;
    }

    public function updateRecord(): bool
    {
        // Get table name
        $tableName = static::tableName();

        // Get primary key
        $primaryKey = static::primaryKey();
        $primaryKeyProperty = $primaryKey['property'];
        $primaryKeyColumn = $primaryKey['column'];

        // Get primary value
        $primaryValue = $this->$primaryKeyProperty;

        // Get model fields
        $fields = $this->fields();

        // Add updatedAt field
        $fields['updatedAt'] = 'updated_at';

        // SQL named placedholders
        $placeholders = array_map(fn($a, $b) => "$b = :$a", array_keys($fields), $fields);

        // Define query
        $sql = "UPDATE $tableName SET " . implode(', ', $placeholders) . " WHERE $primaryKeyColumn = $primaryValue";

        // Prepare query
        $statement = Application::app()->db()->prepare($sql);

        // Bind values
        foreach (array_keys($fields) as $fieldName) {
            // Check for array value
            if (is_array($this->{$fieldName})) {
                $statement->bindValue(":$fieldName", serialize($this->{$fieldName}));
            } else {
                $statement->bindValue(":$fieldName", $this->{$fieldName});
            }
        }

        // Bind current time to the updatedAt field
        $updatedAt = date('Y-m-d H:i:s');
        $statement->bindValue(":updatedAt", $updatedAt);

        // Run query
        $statement->execute();

        // Return
        return true;
    }

    public function deleteRecord(): bool
    {
        // Get table name
        $tableName = static::tableName();

        // Get primary key
        $primaryKey = static::primaryKey();
        $primaryKeyProperty = $primaryKey['property'];
        $primaryKeyColumn = $primaryKey['column'];

        // Get primary value
        $primaryValue = $this->$primaryKeyProperty;

        // Define query
        $sql = "DELETE FROM $tableName WHERE $primaryKeyColumn = $primaryValue";

        // Prepare query
        $statement = Application::app()->db()->prepare($sql);

        // Run query
        $statement->execute();

        // Return
        return true;
    }
}
