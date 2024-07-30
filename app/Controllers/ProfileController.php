<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Models\ProfileModel;

class ProfileController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function profile(Request $request): string
    {
        // Set layout
        //$this->setLayout('default');

        // Create new model instance
        $model = new ProfileModel();

        // Set model property in the application
        Application::app()->setModel($model);

        // Check request
        if ($request->isGet()) {
            // Fetch existing data
            $model->fetchData();
        } elseif ($request->isPost()) {
            // Handle post request
            $model->handlePost($request);
        }

        // Render view
        return self::render('user/profile');
    }
}
