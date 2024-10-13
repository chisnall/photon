<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Traits\FormTrait;

class UserModel extends Model
{
    use FormTrait;

    protected const int STATUS_INACTIVE  = 0;
    protected const int STATUS_ACTIVE  = 1;
    protected const int STATUS_DELETED  = 2;

    protected int $id = 0;
    protected ?string $firstname = null;
    protected ?string $lastname = null;
    protected ?string $email = null;
    protected ?string $password = null;
    protected ?int $status = null;
    protected ?string $token = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;
    protected ?array $settings = null;

    public static function tableName(): string
    {
        return 'users';
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
        return [
            'firstname' => 'first name',
            'lastname' => 'last name',
            'email' => 'e-mail',
            'password' => 'password',
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public function loadSettings(): void
    {
        // Get settings data
        if ($this->id) {
            $settingsData = SettingsModel::getSingleRecord(['userId' => $this->id]);
        } else {
            $settingsData = new SettingsModel();
        }

        // Set property
        $this->settings = $settingsData->getProperty('userSettings');
    }

    public static function id(): int
    {
        return user()->getProperty('id');
    }

    public static function isLoggedIn(): bool
    {
        return (bool)self::id();
    }

    public static function isGuest(): bool
    {
        return !(bool)self::id();
    }

    public static function getDisplayName(): string
    {
        return user()->getProperty('firstname') ?? 'guest';
    }

    public static function generateToken(int $userId): void
    {
        $userData = self::getSingleRecord(['id' => $userId]);

        // Set token
        $userData->token = bin2hex(random_bytes(20));

        // Update record
        $userData->updateRecord();
    }

    public static function login(int $userId): bool
    {
        session()->set('user/id', $userId);
        session()->set('user/token', self::getSingleRecord(['id' => $userId])->token);
        session()->set('user/dbDriver', db()->driver());

        // Only used if implementing the user activation feature
        //session()->remove('user/registered'); // remove registered key so user cannot view the registered page again

        return true;
    }

    public static function logout(): bool
    {
        session()->remove('home');
        session()->remove('response');
        session()->remove('settings');
        session()->remove('status');
        session()->remove('tests');
        session()->remove('user');
        session()->remove('variables');

        return true;
    }
}
