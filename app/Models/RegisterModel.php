<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Request;
use App\Database\SeedExampleCollection;
use App\Exception\AppException;
use Throwable;

class RegisterModel extends UserModel
{
    protected ?string $confirmPassword = null;
    protected string $passwordDisplay = 'hide';
    protected string $confirmPasswordDisplay = 'hide';
    protected ?string $createCollection = null;

    static public function fields(): array
    {
        return [
            'status' => 'status',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'password' => 'password',
            'token' => 'token',
        ];
    }

    public function fieldLabels(): array
    {
        $fieldLabels = parent::fieldLabels();

        $fieldLabels['confirmPassword'] = 'confirm password';

        return $fieldLabels;
    }

    public function rules(): array
    {
        return [
            'firstname' => [self::RULE_REQUIRED],
            'lastname' => [self::RULE_REQUIRED],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL,
                [self::RULE_UNIQUE, 'attributes' => ['email' => '=']]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN_LENGTH, 'min' => 4], [self::RULE_MAX_LENGTH, 'max' => 50]],
            'confirmPassword' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
        ];
    }

    public function insertRecord(): bool
    {
        // Override the parent insertRecord() so we can set various properties
        // but still call the parent at the end

        // Encrypt the password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Set status - currently not requiring the user to activate
        $this->status = self::STATUS_ACTIVE;

        // Set token
        $this->token = bin2hex(random_bytes(20));

        // Run the insertRecord() method on parent class
        return parent::insertRecord();
    }

    public function handlePost(Request $request): void
    {
        // Load data
        $this->loadData($request->getBody());

        // Validate and save data
        if ($this->validate() && $this->insertRecord()) {
            // Get user ID
            $sql = "SELECT id FROM users WHERE email = '" . $this->email . "'";

            try {
                $userId = db()->query($sql)->fetchColumn();
            } catch (Throwable $exception) {
                throw new AppException(message: "Failed to register user.", previous: $exception);
            }

            // Create settings for user
            SettingsModel::createDefaults($userId);

            // Create example collection
            if ($this->createCollection == 'on') {
                $seed = new SeedExampleCollection($this->id);
                $seed->createCollection();
                $seed->createRequests();
            }

            // Login the user
            UserModel::login($userId);

            // Set flash message
            session()->setFlash('success', 'Your account has been registered.');

            // Redirect to home page
            response()->redirect('/');

            // Alternative way that requires the user to activate
            // Need to change user to inactive when creating record above in insertRecord() method

            //// Save user's e-mail in the session so we can show the activate account link
            //// This would actually be implemented with an e-mail sent to the user in production system
            //session()->set('user/registered', $this->email);

            //// Redirect to registered page
            //response()->redirect('/register/registered');
        }
    }

    public function handleActivate(): void
    {
        // Get token
        $token = controller()->data()['token'] ?? null;

        // Check for token
        if ($token) {
            // Get user
            $sql = "SELECT id FROM users WHERE token = '$token'";
            $userId = db()->query($sql)->fetchColumn();

            if ( $userId) {
                // Activate account
                $updatedAt = date('Y-m-d H:i:s');
                $sql = "UPDATE users SET status = 1, updated_at = '$updatedAt' WHERE id = $userId";
                db()->query($sql);

                // Login the user
                UserModel::login($userId);

                // Redirect
                response()->redirect('/register/activated');
            }
        }
    }
}
