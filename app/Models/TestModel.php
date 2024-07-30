<?php

namespace App\Models;

use App\Core\Application;
use App\Core\Model;
use App\Core\Request;
use App\Traits\FormTrait;

class TestModel extends Model
{
    use FormTrait;

    protected ?int $id = null;
    protected ?int $requestId = null;
    protected ?string $testName = null;
    protected ?string $testType = null;
    protected ?string $testAssertion = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;
    protected ?string $formAction = null;

    public static function tableName(): string
    {
        return 'tests';
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
            'requestId' => 'request_id',
            'testName' => 'test_name',
            'testType' => 'test_type',
            'testAssertion' => 'test_assertion',
        ];
    }

    public function fieldLabels(): array
    {
        return [
            'testName' => 'test name',
            'testType' => 'test type',
            'testAssertion' => 'test assertion',
        ];
    }

    public function rules(): array
    {
        // This rule is conditional, depending on the form action

        // Insert action
        if ($this->formAction == 'create') {
            return [
                'testName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'requestId' => '=',
                        'testName' => '='
                        ]
                    ]
                ],
                'testType' => [self::RULE_REQUIRED],
                'testAssertion' => [self::RULE_REQUIRED],
            ];
        }

        // Update action
        if ($this->formAction == 'update') {
            return [
                'testName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'id' => '!=',
                        'requestId' => '=',
                        'testName' => '='
                        ]
                    ]
                ],
                'testType' => [self::RULE_REQUIRED],
                'testAssertion' => [self::RULE_REQUIRED],
            ];
        }

        // For delete action
        return [];
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody(false));

        // Validate
        if ($this->validate()) {
            if ($this->formAction == 'create' && $this->insertRecord()) {
                // Set flash message
                Application::app()->session()->setFlash('info', 'Test has been created.');
            }

            if ($this->formAction == 'update' && $this->updateRecord()) {
                // Set flash message
                Application::app()->session()->setFlash('info', 'Test has been updated.');
            }

            if ($this->formAction == 'delete' && $this->deleteRecord()) {
                // Set flash message
                Application::app()->session()->setFlash('info', 'Test has been deleted.');
            }

            // Redirect to home page
            Application::app()->response()->redirect('/');
        }
    }

    public function formElements(string $formAction): array
    {
        $formElements = [];

        if ($this->formAction == $formAction) {
            $formElements['modalClass'] = 'modal';
            $formElements['id'] = $this->id;
            $formElements['testNameValue'] = $this->testName;
            $formElements['testNameClass'] = $this->getInputClass('testName');
            $formElements['testNameError'] = $this->getError('testName');
            $formElements['testTypeValue'] = $this->testType;
            $formElements['testTypeClass'] = $this->getInputClass('testType');
            $formElements['testTypeError'] = $this->getError('testType');
            $formElements['testAssertionValue'] = $this->testAssertion;
            $formElements['testAssertionClass'] = $this->getInputClass('testAssertion');
            $formElements['testAssertionError'] = $this->getError('testAssertion');
        } else {
            $formElements['modalClass'] = 'modal hidden';
            $formElements['id'] = null;
            $formElements['testNameValue'] = null;
            $formElements['testNameClass'] = 'input-normal';
            $formElements['testNameError'] = null;
            $formElements['testTypeValue'] = null;
            $formElements['testTypeClass'] = 'input-normal';
            $formElements['testTypeError'] = null;
            $formElements['testAssertionValue'] = null;
            $formElements['testAssertionClass'] = 'input-normal';
            $formElements['testAssertionError'] = null;
        }

        return $formElements;
    }
}
