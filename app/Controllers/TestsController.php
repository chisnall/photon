<?php

declare(strict_types=1);

namespace App\Controllers;

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
        app()->setModel($GroupModel);

        // Check request
        if ($request->isGet()) {

            // Check if user has logged in
            if (session()->get('tests/layout/initUser')) {
                // Init user
                // The tests view page will set the value to false
                $this->initUser();
            }

            // Get select / unselect
            $select = $this->data['select'] ?? null;
            $unselect = $this->data['unselect'] ?? null;

            // Group - select
            if ($select == 'group') {
                // Get group ID
                $groupId = (int)$this->data['id'];

                // Handle session
                if (GroupModel::handleSession($groupId)) {
                    // Save to settings
                    SettingsModel::updateSetting('tests/left/groupId', (int)$groupId);
                }

                // Redirect to tests
                response()->redirect('/tests');
            }

            // Group - unselect
            if ($unselect == 'group') {
                // Clear session
                if (GroupModel::clearSession()) {
                    // Save to settings
                    SettingsModel::updateSetting('tests/left/groupId', null);
                }

                // Redirect to tests
                response()->redirect('/tests');
            }

        } elseif ($request->isPost()) {
            // Get model class name
            $modelClassName = $request->getBody()['modelClassName'];

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
        if ($left_selectedTab) session()->set('tests/left/selectedTab', $left_selectedTab);
        if ($upper_selectedTab) session()->set('tests/upper/selectedTab', $upper_selectedTab);
        if ($lower_selectedTab) session()->set('tests/lower/selectedTab', $lower_selectedTab);

        // Group and session handlers
        if ($groupId) GroupModel::handleSession($groupId);
    }
}
