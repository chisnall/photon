<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database\Migrations;
use App\Core\Request;
use App\Exception\AppException;
use Throwable;

class LoginModel extends UserModel
{
    protected string $passwordDisplay = 'hide';
    protected ?string $formAction = null;

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL,
                [self::RULE_USER_EXISTS, 'attributes' => [
                    'where' => 'email',
                    ]
                ],
                [self::RULE_USER_ACTIVE, 'attributes' => [
                    'select' => 'status',
                    'value' => 1,
                    'where' => 'email',
                    ]
                ]
            ],
            'password' => [self::RULE_REQUIRED,
                [self::RULE_PASSWORD_VERIFY, 'attributes' => [
                    'emailField' => 'email',
                    'passwordField' => 'password',
                    ]
                ]
            ]
        ];
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody());

        // Validate user
        if ($this->validate()) {
            // Build SQL
            $sql = "SELECT id FROM users WHERE email = :email";

            try {
                // Run statement
                $statement = db()->prepare($sql);
                $statement->bindValue(":email", $this->email);
                $statement->execute();
                $userId = $statement->fetchColumn();
            } catch (Throwable $exception) {
                throw new AppException(message: "Failed to login user.", previous: $exception);
            }

            // Cleanup old session files
            cleanupSessions();

            // Get pending migrations and set session
            $pendingMigrations = (new Migrations())->getPendingMigrations();
            session()->set('status/pendingMigrations', (bool)$pendingMigrations);

            // Generate new token
            UserModel::generateToken($userId);

            // Login user
            UserModel::login($userId);

            // Check if settings record is missing - ID value won't be present
            if (!SettingsModel::getSingleRecord(['userId' => $userId])->getProperty('id')) {
                // Create default settings
                SettingsModel::createDefaults($userId);
            }

            // Remove layout settings from guest user
            session()->remove('home/layout');
            session()->remove('tests/layout');

            // Set home and tests page to init the layout
            session()->set("home/layout/initUser", true);
            session()->set("tests/layout/initUser", true);

            // Set flash message
            session()->setFlash('success', 'You have logged in.');

            // Check for pending migrations
            if ($pendingMigrations) {
                // Redirect to pending migrations page
                response()->redirect('/migrations');
            } else {
                // Redirect to referer page
                response()->redirect(session()->getReferer());
            }
        }
    }
}
