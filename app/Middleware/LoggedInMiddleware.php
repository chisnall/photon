<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Application;
use App\Core\Middleware;
use App\Models\UserModel;

class LoggedInMiddleware extends Middleware
{
    public function __construct(protected array $actions = [])
    {
    }

    public function execute(): void
    {
        // Check if logged in
        if (UserModel::isLoggedIn()) {
            if (array_key_exists(Application::app()->controller()->getProperty('action'), $this->actions)) {
                // Get action - the method we call in the controller
                $action = Application::app()->controller()->getProperty('action');

                // Get redirect location, type and message
                $redirect = $this->actions[$action][0];
                $type = $this->actions[$action][1] ?? null;
                $message = $this->actions[$action][2] ?? null;

                // Flash message
                if ( $type && $message ) {
                    Application::app()->session()->setFlash($type, $message);
                }

                // Redirect
                Application::app()->response()->redirect($redirect);
            }
        }
    }
}
