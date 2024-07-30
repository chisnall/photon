<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Models\SettingsModel;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function settings(Request $request): string
    {
        // Create new model instance
        $model = new SettingsModel();

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
        return self::render('user/settings');
    }

    public function defaults()
    {
        // Set defaults
        SettingsModel::createDefaults(Application::app()->user()->id());

        // Set flash message
        Application::app()->session()->setFlash('info', 'Default settings have been loaded.');

        // Redirect to settings
        Application::app()->response()->redirect('/settings');
    }
}
