<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Models\GroupModel;
use App\Models\SettingsModel;

class TestsController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function tests(Request $request): string
    {
        // Set layout
        $this->setLayout('home');

        // Create new model instances
        $GroupModel = new GroupModel();

        // Set model properties in the application
        Application::app()->setModel($GroupModel);

        // Check request
        if ($request->isGet()) {

            // Check if user has logged in
            if (Application::app()->session()->get('tests/layout/initUser')) {
                // Init user
                // The tests view page will set the value to false
                $this->initUser();
            }

            // Get select / unselect
            $select = $_GET['select'] ?? null;
            $unselect = $_GET['unselect'] ?? null;

            // Group - select
            if ($select == 'group') {
                // Get group ID
                $groupId = $_GET['id'];

                // Handle session
                if (GroupModel::handleSession($groupId)) {
                    // Save to settings
                    SettingsModel::updateSetting('tests/left/groupId', (int)$groupId);
                }

                // Redirect to tests
                Application::app()->response()->redirect('/tests');
            }

            // Group - unselect
            if ($unselect == 'group') {
                // Remove selected group ID and name
                Application::app()->session()->remove('tests/left/groupId');
                Application::app()->session()->remove('tests/left/groupName');

                // Clear upper data
                Application::app()->session()->remove('tests/upper');

                // Save to settings
                SettingsModel::updateSetting('tests/left/groupId', null);

                // Redirect to tests
                Application::app()->response()->redirect('/tests');
            }

        } elseif ($request->isPost()) {
            // Get model class name
            $modelClassName = $_POST['modelClassName'];

            // Handle post request
            $$modelClassName->handlePost($request);
        }

        // Render view
        return self::render('tests');
    }

    public function initUser()
    {
        // Get settings for home view
        $groupId = SettingsModel::getSetting('tests/left/groupId', true);
        $left_selectedTab = SettingsModel::getSetting('tests/left/selectedTab', true);
        $upper_selectedTab = SettingsModel::getSetting('tests/upper/selectedTab', true);
        $lower_selectedTab = SettingsModel::getSetting('tests/lower/selectedTab', true);

        // Set session
        if ($left_selectedTab) Application::app()->session()->set('tests/left/selectedTab', $left_selectedTab);
        if ($upper_selectedTab) Application::app()->session()->set('tests/upper/selectedTab', $upper_selectedTab);
        if ($lower_selectedTab) Application::app()->session()->set('tests/lower/selectedTab', $lower_selectedTab);

        // Group and session handlers
        if ($groupId) GroupModel::handleSession($groupId);
    }
}
