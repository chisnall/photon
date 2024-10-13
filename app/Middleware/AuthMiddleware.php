<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\UserModel;

class AuthMiddleware extends \App\Core\Middleware
{
    public function __construct(protected array $actions = [])
    {
    }

    public function execute(): void
    {
        // Check for guest
        if (UserModel::isGuest()) {
            // Actions = methods in the controller
            // Do not allow access on an empty actions array
            // Do not allow access if the action is specified in the actions array
            if (count($this->actions) === 0 || in_array(controller()->getProperty('action'), $this->actions)) {
                // Alternative to redirect - show error
                //$exception = new ForbiddenException();
                //ExceptionHandler::client(message: $exception->getMessage(), exception: $exception);

                // Redirect to login page
                response()->redirect('/login');
            }
        }
    }
}
