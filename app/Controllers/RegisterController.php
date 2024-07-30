<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\LoggedInMiddleware;
use App\Models\RegisterModel;
use App\Models\UserModel;

class RegisterController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new LoggedInMiddleware([
            'register' => ['/profile', 'info', 'You are already registered.'],
            'activate' => ['/profile', 'info', 'Your account is already activated.'],
        ]));
    }

    public function register(Request $request): string
    {
        // Set layout
        //$this->setLayout('default');

        // Create new register model instance
        $model = new RegisterModel();

        // Set model property in the application
        Application::app()->setModel($model);

        // Check for post
        if ($request->isPost()) {
            // Handle post request
            $model->handlePost($request);
        }

        // Render view
        return self::render('register/register');
    }

    public function registered(): string
    {
        // Check that the user has actually registered
        if (Application::app()->session()->get('user/registered')) {
            return self::render('register/registered');
        }

        // Redirect to homepage
        Application::app()->response()->redirect('/');
    }

    public function activate(): string
    {
        // Create new model instance
        $model = new RegisterModel();

        // Handle post request
        $model->handleActivate();

        // Render view - this will render if the token is not valid
        return self::render('register/activate');
    }

    public function activated(): string
    {
        // Confirm user is logged in
        if (UserModel::isLoggedIn()) {
            return self::render('register/activated');
        }

        // Redirect to homepage
        Application::app()->response()->redirect('/');
    }
}
