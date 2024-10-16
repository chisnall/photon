<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
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
        app()->setModel($CollectionModel);
        app()->setModel($RequestModel);
        app()->setModel($TestModel);

        // Check request
        if ($request->isGet()) {
            // Check if user has logged in
            if (session()->get('home/layout/initUser')) {
                // Init user
                // The home view page will set the value to false
                $this->initUser();
            }

            // Get select / unselect
            $select = $this->data['select'] ?? null;
            $unselect = $this->data['unselect'] ?? null;

            // Collection - select
            if ($select == 'collection') {
                // Get collection ID
                $collectionId = (int)$this->data['id'];

                // Handle session
                if (CollectionModel::handleSession($collectionId)) {
                    // Save to settings
                    SettingsModel::updateSetting('home/left/collectionId', (int)$collectionId);
                }

                // Redirect to homepage
                response()->redirect('/');
            }

            // Collection - unselect
            if ($unselect == 'collection') {
                // Remove selected collection ID and name
                session()->remove('home/left/collectionId');
                session()->remove('home/left/collectionName');

                // Save to settings
                SettingsModel::updateSetting('home/left/collectionId', null);

                // Redirect to homepage
                response()->redirect('/');
            }

            // Request - select
            if ($select == 'request') {
                // Get request ID
                $requestId = (int)$this->data['id'];

                // Handle session
                if (RequestModel::handleSession($requestId)) {
                    // Save to settings
                    SettingsModel::updateSetting('home/left/requestId', (int)$requestId);
                }

                // Redirect to homepage
                response()->redirect('/');
            }

            // Request - unselect
            if ($unselect == 'request') {
                // Clear session
                if (RequestModel::clearSession()) {
                    // Save to settings
                    SettingsModel::updateSetting('home/left/requestId', null);
                }

                // Redirect to homepage
                response()->redirect('/');
            }

            // Variable - reset button
            if ($select == 'variable') {
                // Get variable collection ID and name
                $variableCollectionId = (int)$this->data['collection'];
                $variableName = $this->data['variable'];

                // Remove variable
                session()->remove("variables/$variableCollectionId/$variableName");

                // Redirect to homepage
                response()->redirect('/');
            }

            // Settings - update button
            if ($select == 'settings') {
                // Get tab
                $tab = $this->data['tab'];

                // Save tab to session
                session()->set('settings/selectedTab', $tab);

                // Redirect to settings
                response()->redirect('/settings');
            }

        } elseif ($request->isPost()) {
            // Get model class name
            $modelClassName = $request->getBody()['modelClassName'];

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
        if ($left_selectedTab) session()->set('home/left/selectedTab', $left_selectedTab);
        if ($upper_selectedTab) session()->set('home/upper/selectedTab', $upper_selectedTab);
        if ($lower_selectedTab) session()->set('home/lower/selectedTab', $lower_selectedTab);

        // Collection and request session handlers
        if ($collectionId) CollectionModel::handleSession($collectionId);
        if ($requestId) RequestModel::handleSession($requestId);
    }

    public function html(): never
    {
        // For the /html iframe on the home page
        includeFile(file: '/app/Views/home-browser.php');
        exit;
    }
}
