<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\Functions;
use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Models\CollectionModel;
use App\Models\RequestModel;
use App\Models\SettingsModel;
use App\Models\TestModel;

class HomeController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function home(Request $request): string
    {
        // Set layout
        $this->setLayout('home');

        // Create new model instances
        $CollectionModel = new CollectionModel();
        $RequestModel = new RequestModel();
        $TestModel = new TestModel();

        // Set model properties in the application
        Application::app()->setModel($CollectionModel);
        Application::app()->setModel($RequestModel);
        Application::app()->setModel($TestModel);

        // Check request
        if ($request->isGet()) {
            // Check if user has logged in
            if (Application::app()->session()->get('home/layout/initUser')) {
                // Init user
                // The home view page will set the value to false
                $this->initUser();
            }

            // Get select / unselect
            $select = $_GET['select'] ?? null;
            $unselect = $_GET['unselect'] ?? null;

            // Collection - select
            if ($select == 'collection') {
                // Get collection ID
                $collectionId = $_GET['id'];

                // Handle session
                if (CollectionModel::handleSession($collectionId)) {
                    // Save to settings
                    SettingsModel::updateSetting('home/left/collectionId', (int)$collectionId);
                }

                // Redirect to homepage
                Application::app()->response()->redirect('/');
            }

            // Collection - unselect
            if ($unselect == 'collection') {
                // Remove selected collection ID and name
                Application::app()->session()->remove('home/left/collectionId');
                Application::app()->session()->remove('home/left/collectionName');

                // Save to settings
                SettingsModel::updateSetting('home/left/collectionId', null);

                // Redirect to homepage
                Application::app()->response()->redirect('/');
            }

            // Request - select
            if ($select == 'request') {
                // Get request ID
                $requestId = $_GET['id'];

                // Handle session
                if (RequestModel::handleSession($requestId)) {
                    // Save to settings
                    SettingsModel::updateSetting('home/left/requestId', (int)$requestId);
                }

                // Redirect to homepage
                Application::app()->response()->redirect('/');
            }

            // Request - unselect
            if ($unselect == 'request') {
                // Register session variables
                Application::app()->session()->set('home/upper/requestModified', false);

                // Remove selected request ID and name
                Application::app()->session()->remove('home/left/requestId');
                Application::app()->session()->remove('home/left/requestName');

                // Remove request error
                Application::app()->session()->remove('tests/upper/requestError');

                // Clear response session data
                Application::app()->session()->remove('response');

                // Save to settings
                SettingsModel::updateSetting('home/left/requestId', null);

                // Redirect to homepage
                Application::app()->response()->redirect('/');
            }

            // Variable - reset button
            if ($select == 'variable') {
                // Get variable collection ID and name
                $variableCollectionId = $_GET['collection'];
                $variableName = $_GET['variable'];

                // Remove variable
                Application::app()->session()->remove("variables/$variableCollectionId/$variableName");

                // Redirect to homepage
                Application::app()->response()->redirect('/');
            }

            // Settings - update button
            if ($select == 'settings') {
                // Get tab
                $tab = $_GET['tab'];

                // Save tab to session
                Application::app()->session()->set('settings/selectedTab', $tab);

                // Redirect to settings
                Application::app()->response()->redirect('/settings');
            }

        } elseif ($request->isPost()) {
            // Get model class name
            $modelClassName = $_POST['modelClassName'];

            // Handle post request
            $$modelClassName->handlePost($request);
        }

        // Render view
        return self::render('home');
    }

    public function initUser(): void
    {
        // Get settings for home view
        $collectionId = SettingsModel::getSetting('home/left/collectionId', true);
        $requestId = SettingsModel::getSetting('home/left/requestId', true);
        $left_selectedTab = SettingsModel::getSetting('home/left/selectedTab', true);
        $upper_selectedTab = SettingsModel::getSetting('home/upper/selectedTab', true);
        $lower_selectedTab = SettingsModel::getSetting('home/lower/selectedTab', true);

        // Set session
        if ($left_selectedTab) Application::app()->session()->set('home/left/selectedTab', $left_selectedTab);
        if ($upper_selectedTab) Application::app()->session()->set('home/upper/selectedTab', $upper_selectedTab);
        if ($lower_selectedTab) Application::app()->session()->set('home/lower/selectedTab', $lower_selectedTab);

        // Collection and request session handlers
        if ($collectionId) CollectionModel::handleSession($collectionId);
        if ($requestId) RequestModel::handleSession($requestId);
    }

    public function html(): never
    {
        // For the /html iframe on the home page
        Functions::includeFile(file: '/app/Views/home-browser.php');
        exit;
    }
}
