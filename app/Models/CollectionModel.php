<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\Model;
use App\Core\Request;
use App\Traits\FormTrait;

class CollectionModel extends Model
{
    use FormTrait;

    protected ?int $id = null;
    protected ?int $userId = null;
    protected ?string $collectionName = null;
    protected ?array $collectionVariables = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;
    protected ?string $formAction = null;

    public static function tableName(): string
    {
        return 'collections';
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
            'collectionName' => 'collection_name',
            'collectionVariables' => 'collection_variables',
        ];
    }

    public function fieldLabels(): array
    {
        return [
            'collectionName' => 'collection name',
            'collectionVariables' => 'collection variables',
        ];
    }

    public function rules(): array
    {
        // This rule is conditional, depending on the form action

        // Insert action
        if ($this->formAction == 'create') {
            return [
                'collectionName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'userId' => '=',
                        'collectionName' => '='
                        ]
                    ]
                ],
            ];
        }

        // Update action
        if ($this->formAction == 'update') {
            return [
                'collectionName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'id' => '!=',
                        'userId' => '=',
                        'collectionName' => '='
                        ]
                    ]
                ],
            ];
        }

        // For delete action
        return [];
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody());

        // Create
        if ($this->formAction == 'create') {
            // Set properties
            $this->collectionVariables = [];
        }

        // Update
        if ($this->formAction == 'update') {
            // Get table data
            $collectionVariableEnabledArray = $_POST['collectionVariableEnabled'];
            $collectionVariableNameArray = $_POST['collectionVariableName'];
            $collectionVariableValueArray = $_POST['collectionVariableValue'];

            // Process table data
            $collectionVariablesInputs = [];
            foreach ($collectionVariableNameArray as $inputIndex => $inputName) {
                $inputEnabled = $collectionVariableEnabledArray[$inputIndex];
                $inputValue = $collectionVariableValueArray[$inputIndex];
                if ($inputEnabled) {
                    $collectionVariablesInputs[] = [
                        'name' => trim($inputName),
                        'value' => trim($inputValue),
                        'enabled' => $inputEnabled,
                    ];
                }
            }

            // Set properties
            $this->collectionVariables = $collectionVariablesInputs;
        }

        // Validate
        if ($this->validate()) {
            if ($this->formAction == 'create' && $this->insertRecord()) {
                // Register session variables
                Application::app()->session()->set('home/left/collectionId', $this->id);
                Application::app()->session()->set('home/left/collectionName', $this->collectionName);
                Application::app()->session()->set('home/left/collectionVariables', $this->collectionVariables);

                // Update settings
                SettingsModel::updateSetting('home/left/collectionId', $this->id);

                // Set flash message
                Application::app()->session()->setFlash('info', 'Collection has been created.');
            }

            if ($this->formAction == 'update' && $this->updateRecord()) {
                // Register session variables
                Application::app()->session()->set('home/left/collectionName', $this->collectionName);
                Application::app()->session()->set('home/left/collectionVariables', $this->collectionVariables);

                // Set flash message
                Application::app()->session()->setFlash('info', 'Collection has been updated.');
            }

            if ($this->formAction == 'delete' && $this->deleteRecord()) {
                // Check for selected request
                if (Application::app()->session()->get('home/left/requestId')) {
                    // Check if the selected request still exists
                    $requestData = RequestModel::getSingleRecord(['id' => Application::app()->session()->get('home/left/requestId')]);
                    if (!$requestData->getProperty('id')) {
                        // Request record was deleted when the collection record was deleted

                        // Remove session variable
                        Application::app()->session()->remove('home/left/requestId');
                        Application::app()->session()->remove('home/left/requestName');

                        // Update settings
                        SettingsModel::deleteSetting("home/left/requestId");
                    }
                }

                // Remove session variables
                Application::app()->session()->remove('home/left/collectionId');
                Application::app()->session()->remove('home/left/collectionName');
                Application::app()->session()->remove('home/left/collectionVariables');

                // Clear request session
                RequestModel::clearSession();

                // Update settings
                SettingsModel::deleteSetting("home/left/collectionId");

                // Set flash message
                Application::app()->session()->setFlash('info', 'Collection has been deleted.');
            }

            // Redirect to home page
            Application::app()->response()->redirect('/');
        }
    }

    public static function handleSession(int $collectionId): bool
    {
        // Get collection details
        $collectionData = self::getSingleRecord(['id' => $collectionId]);
        $collectionUserId = $collectionData->getProperty('userId');
        $collectionName = $collectionData->getProperty('collectionName');
        $collectionVariables = $collectionData->getProperty('collectionVariables');

        // Confirm user ID matches logged in user ID
        if ($collectionUserId == Application::app()->user()->id()) {
            // Register session variables
            Application::app()->session()->set('home/left/collectionId', $collectionId);
            Application::app()->session()->set('home/left/collectionName', $collectionName);
            Application::app()->session()->set('home/left/collectionVariables', $collectionVariables);

            return true;
        }

        return false;
    }

    public function formElements(string $formAction): array
    {
        $formElements = [];

        if ($this->formAction == $formAction) {
            $formElements['modalClass'] = 'modal';
            $formElements['id'] = $this->id;
            $formElements['collectionNameValue'] = $this->collectionName;
            $formElements['collectionNameClass'] = $this->getInputClass('collectionName');
            $formElements['collectionNameError'] = $this->getError('collectionName');
            $formElements['collectionVariablesValue'] = $this->collectionVariables;
        } else {
            $formElements['modalClass'] = 'modal hidden';
            $formElements['id'] = null;
            $formElements['collectionNameValue'] = null;
            $formElements['collectionNameClass'] = 'input-normal';
            $formElements['collectionNameError'] = null;
            $formElements['collectionVariablesValue'] = null;
        }

        return $formElements;
    }

    public static function variables(int $id): array
    {
        $variables = [];

        // Get collection variables
        $collectionVariables = self::getSingleRecord(['id' => $id])->getProperty('collectionVariables') ?? [];

        // Process collection variables
        foreach ($collectionVariables as $collectionVariable) {
            if ($collectionVariable['enabled'] == 'on') {
                $variables[$collectionVariable['name']] = ['value' => $collectionVariable['value'], 'type' => 'collection'];
            }
        }

        return $variables;
    }
}
