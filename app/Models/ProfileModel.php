<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Application;
use App\Core\Request;

class ProfileModel extends UserModel
{
    protected ?string $newPassword = null;
    protected ?string $confirmNewPassword = null;
    protected string $newPasswordDisplay = 'hide';
    protected string $confirmNewPasswordDisplay = 'hide';

    static public function fields(): array
    {
        return [
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'password' => 'password',
        ];
    }

    public function fieldLabels(): array
    {
        $fieldLabels = parent::fieldLabels();

        $fieldLabels['newPassword'] = 'new password';
        $fieldLabels['confirmNewPassword'] = 'confirm new password';

        return $fieldLabels;
    }

    public function rules(): array
    {
        $rules = [
            'firstname' => [self::RULE_REQUIRED],
            'lastname' => [self::RULE_REQUIRED],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL,
                [self::RULE_UNIQUE, 'attributes' => [
                    'id' => '!=',
                    'email' => '='
                    ]
                ]
            ],
        ];

        // This rule is conditional, if the new password is provided
        if ($this->newPassword) {
            $rules['newPassword'] = [[self::RULE_MIN_LENGTH, 'min' => 4], [self::RULE_MAX_LENGTH, 'max' => 10]];
            $rules['confirmNewPassword'] = [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'newPassword']];
        }

        return $rules;
    }

    public function fetchData(): void
    {
        $this->id = Application::app()->user()->getProperty('id') ?? null;
        $this->firstname = Application::app()->user()->getProperty('firstname') ?? null;
        $this->lastname = Application::app()->user()->getProperty('lastname') ?? null;
        $this->email = Application::app()->user()->getProperty('email') ?? null;
    }

    public function updateRecord(): bool
    {
        // Override the parent updateRecord() so we can set various properties
        // but still call the parent at the end

        // Get existing password
        $this->password = Application::app()->user()->password ?? null;

        // Check for new password
        if ($this->newPassword) {
            // Encrypt the new password
            $this->newPassword = password_hash($this->newPassword, PASSWORD_DEFAULT);

            // Replace the existing password
            $this->password = $this->newPassword;
        }

        // Run the updateRecord() method on parent class
        return parent::updateRecord();
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody());

        // Validate and save data
        if ($this->validate() && $this->updateRecord()) {
            // Set flash message
            Application::app()->session()->setFlash('info', 'Profile has been updated.');

            // Redirect to profile page
            Application::app()->response()->redirect('/profile');
        } else {
            // Set flash with removal set to true, otherwise it will be shown again on page reload
            Application::app()->session()->setFlash('warning', 'Profile failed to update.', true);
        }
    }
}
