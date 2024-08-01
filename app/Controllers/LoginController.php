<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\LoggedInMiddleware;
use App\Models\LoginModel;
use App\Models\UserModel;

class LoginController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new LoggedInMiddleware([
            'login' => ['/', 'info', 'You are already logged in.'],
        ]));
    }

    public function login(Request $request): string
    {
        // Set layout
        $this->setLayout('default');

        // Create new model instance
        $model = new LoginModel();

        // Set model property in the application
        Application::app()->setModel($model);

        // Check for post
        if ($request->isPost()) {
            // Handle post request
            $model->handlePost($request);
        }

        // Render the view
        return self::render('user/login');
    }

    public function logout(): void
    {
        // Set flash message
        Application::app()->session()->setFlash('success', 'You have logged out.');

        // Logout user
        UserModel::logout();

        // Redirect to homepage
        Application::app()->response()->redirect('/');
    }
}
