<?php

namespace App\Models;

use App\Core\Application;
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
        return Application::app()->user()->getProperty('id');
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
        return Application::app()->user()->getProperty('firstname') ?? 'guest';
    }

    public static function login($userId): bool
    {
        Application::app()->session()->set('user/id', $userId);
        Application::app()->session()->set('user/dbDriver', Application::app()->db()->driver());

        // Only used if implementing the user activation feature
        //Application::app()->session()->remove('user/registered'); // remove registered key so user cannot view the registered page again

        return true;
    }

    public static function logout(): bool
    {
        Application::app()->session()->remove('home');
        Application::app()->session()->remove('response');
        Application::app()->session()->remove('settings');
        Application::app()->session()->remove('status');
        Application::app()->session()->remove('tests');
        Application::app()->session()->remove('user');
        Application::app()->session()->remove('variables');

        return true;
    }
}
