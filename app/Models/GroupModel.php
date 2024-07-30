<?php

namespace App\Models;

use App\Core\Application;
use App\Core\Model;
use App\Core\Request;
use App\Traits\FormTrait;

class GroupModel extends Model
{
    use FormTrait;

    protected ?int $id = null;
    protected ?int $userId = null;
    protected ?string $groupName = null;
    protected ?array $groupRequests = null;
    protected ?array $groupRequestsAdd = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;
    protected ?string $formAction = null;

    public static function tableName(): string
    {
        return 'groups';
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
            'groupName' => 'group_name',
            'groupRequests' => 'group_requests',
        ];
    }

    public function fieldLabels(): array
    {
        return [
            'groupName' => 'group name',
            'groupRequests' => 'group requests',
        ];
    }

    public function rules(): array
    {
        // This rule is conditional, depending on the form action

        // Create action
        if ($this->formAction == 'save') {
            return [
                'groupName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'id' => '!=',
                        'userId' => '=',
                        'groupName' => '='
                       ]
                    ]
                ],
            ];
        }

        // Create action
        if ($this->formAction == 'create') {
            return [
                'groupName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'userId' => '=',
                        'groupName' => '='
                        ]
                    ]
                ],
            ];
        }

        // Clone action
        if ($this->formAction == 'clone') {
            return [
                'groupName' => [self::RULE_REQUIRED,
                    [self::RULE_UNIQUE, 'attributes' => [
                        'userId' => '=',
                        'groupName' => '='
                        ]
                    ]
                ],
            ];
        }

        // Add requests action
        if ($this->formAction == 'addRequests') {
            return [
                'groupRequestsAdd' => [self::RULE_REQUIRED],
            ];
        }

        return [];
    }

    public function displayError(): ?string
    {
        // Get errors
        $groupNameError = $this->getError('groupName');

        // Request URL
        if ($groupNameError) {
            if ($groupNameError == 'This field is required') $groupNameError = 'enter the group name';
            elseif ($groupNameError == 'Record with group name already exists') $groupNameError = 'group name is already in use';
            return $groupNameError;
        }

        return null;
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody());

        // Save
        if ($this->formAction == 'save') {
            // Handle tables

            // Get table data
            $groupRequestIdArray = $_POST['groupRequestId'] ?? null;
            $groupRequestEnabledArray = $_POST['groupRequestEnabled'] ?? null;

            // Arrays to hold data
            $groupRequestsInputs = [];

            // Process requests table
            if ($groupRequestIdArray) {
                foreach ($groupRequestIdArray as $groupRequestKey => $groupRequestId) {
                    $groupRequestEnabled = $groupRequestEnabledArray[$groupRequestKey];
                    if ($groupRequestEnabled) {
                        // Store form data
                        $groupRequestsInputs[] = [
                            'id' => $groupRequestId,
                            'enabled' => $groupRequestEnabled,
                        ];
                    }
                }
            }

            // Set properties
            $this->groupRequests = $groupRequestsInputs;

            // Save to session - inputs
            Application::app()->session()->set('tests/upper/groupName', $this->groupName);
            Application::app()->session()->set('tests/upper/groupRequests', $this->groupRequests);

            // Save to session - tabs
            Application::app()->session()->set('tests/left/selectedTab', $_POST['left_selectedTab']);
            Application::app()->session()->set('tests/upper/selectedTab', $_POST['upper_selectedTab']);
            Application::app()->session()->set('tests/lower/selectedTab', $_POST['lower_selectedTab']);
        }

        // Clone
        if ($this->formAction == 'clone') {
            // Get group details
            $groupData = self::getSingleRecord(['id' => $this->id]);
            $groupRequests = $groupData->getProperty('groupRequests');

            // Set properties
            $this->groupRequests = $groupRequests;
        }

        // Add requests
        if ($this->formAction == 'addRequests') {
            // Get existing requests
            $groupRequests = Application::app()->session()->get('tests/upper/groupRequests');

            // Process request IDs
            if ($this->groupRequestsAdd) {
                foreach ($this->groupRequestsAdd as $requestId) {
                    // Add to existing requests
                    $groupRequests[] = [
                        'id' => $requestId,
                        'enabled' => 'on',
                    ];
                }
            }

            // Set properties
            $this->groupRequests = $groupRequests;
        }

        // Validate
        if ($this->validate()) {
            if ($this->formAction == 'save' && $this->updateRecord()) {
                // Register session variables
                Application::app()->session()->set('tests/left/groupName', $this->groupName);
                Application::app()->session()->set('tests/upper/groupModified', false);

                // Remove group error
                Application::app()->session()->remove('tests/upper/groupError');

                // Set flash message
                Application::app()->session()->setFlash('info', 'Group has been updated.');
            }

            if (($this->formAction == 'create' || $this->formAction == 'clone') && $this->insertRecord()) {
                // Register session variables
                Application::app()->session()->set('tests/left/groupId', $this->id);
                Application::app()->session()->set('tests/left/groupName', $this->groupName);
                Application::app()->session()->set('tests/upper/groupName', $this->groupName);
                Application::app()->session()->set('tests/upper/groupRequests', $this->groupRequests);
                Application::app()->session()->set('tests/upper/groupModified', false);

                // Remove group error
                Application::app()->session()->remove('tests/upper/groupError');

                // Update settings
                SettingsModel::updateSetting('tests/left/groupId', $this->id);

                // Set flash message
                Application::app()->session()->setFlash('info', 'Group has been ' . $this->formAction . 'd.');
            }

            if ($this->formAction == 'addRequests') {
                // This does not update the database record
                // Only the session is updated

                // Register session variables
                Application::app()->session()->set('tests/upper/groupRequests', $this->groupRequests);
                Application::app()->session()->set('tests/upper/groupModified', true);

                // Set flash message
                if (count($this->groupRequestsAdd) > 1) {
                    Application::app()->session()->setFlash('info', 'Requests have been added.');
                } else {
                    Application::app()->session()->setFlash('info', 'Request has been added.');
                }
            }

            if ($this->formAction == 'delete' && $this->deleteRecord()) {
                // Remove session variables
                Application::app()->session()->remove('tests/left/groupId');
                Application::app()->session()->remove('tests/left/groupName');
                Application::app()->session()->remove('tests/upper/groupName');
                Application::app()->session()->remove('tests/upper/groupRequests');

                // Update settings
                SettingsModel::deleteSetting("tests/left/groupId");

                // Set flash with removal set to true, otherwise it will be shown again on page reload
                Application::app()->session()->setFlash('info', 'Group has been deleted.', true);
            }

            // Redirect to tests page
            Application::app()->response()->redirect('/tests');
        } else {
            if ($this->formAction == 'save') {
                // Register session variables
                Application::app()->session()->set('tests/upper/groupModified', true);
                Application::app()->session()->set('tests/upper/groupError', $this->displayError());

                // Set flash with removal set to true, otherwise it will be shown again on page reload
                Application::app()->session()->setFlash('warning', 'Error: ' . $this->displayError(), true);
            }
        }
    }

    public static function handleSession(int $groupId): bool
    {
        // Get group details
        $groupData = self::getSingleRecord(['id' => $groupId]);
        $groupUserId = $groupData->getProperty('userId');
        $groupName = $groupData->getProperty('groupName');

        // Confirm user ID matches logged in user ID
        if ($groupUserId == Application::app()->user()->id()) {
            // Register session variables
            Application::app()->session()->set('tests/left/groupId', $groupId);
            Application::app()->session()->set('tests/left/groupName', $groupName);
            Application::app()->session()->set('tests/upper/groupName', $groupName);
            Application::app()->session()->set('tests/upper/groupRequests', $groupData->getProperty('groupRequests'));
            Application::app()->session()->set('tests/upper/groupModified', false);

            // Remove group error
            Application::app()->session()->remove('tests/upper/groupError');

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
            $formElements['groupNameValue'] = $this->groupName;
            $formElements['groupNameClass'] = $this->getInputClass('groupName');
            $formElements['groupNameError'] = $this->getError('groupName');
            $formElements['groupRequestsAddValue'] = $this->groupRequestsAdd;
            $formElements['groupRequestsAddClass'] = $this->getInputClass('groupRequestsAdd');
            $formElements['groupRequestsAddError'] = $this->getError('groupRequestsAdd');
        } else {
            $formElements['modalClass'] = 'modal hidden';
            $formElements['id'] = null;
            $formElements['groupNameValue'] = null;
            $formElements['groupNameClass'] = 'input-normal';
            $formElements['groupNameError'] = null;
            $formElements['groupRequestsAddValue'] = null;
            $formElements['groupRequestsAddClass'] = 'input-normal';
            $formElements['groupRequestsAddError'] = null;
        }

        return $formElements;
    }
}
