<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Controller;
use App\Core\ExceptionHandler;
use App\Core\Request;
use App\Exception\AppException;
use App\Exception\BadRequestException;
use App\Middleware\AuthMiddleware;
use App\Models\CollectionModel;
use App\Models\TestModel;
use Exception;

class SiteController extends Controller
{
    public function __construct()
    {
        // Run constructor in parent
        parent::__construct();

        // Register middleware
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function genericView(): string
    {
        // Render generic view
        return self::render($this->view);
    }

    public function test(Request $request): string
    {
        // An example of passing an associative array to the view
        $params = [
            "var1" => "string here",
            "var2" => 123456,
        ];

        // Create new model instances
        //$CollectionModel = new CollectionModel();
        //$TestModel = new TestModel();

        // Set model properties in the application
        //Application::app()->setModel($CollectionModel);
        //Application::app()->setModel($TestModel);

        // Check for post
        if ($request->isPost()) {
            // Get model class name
            $modelClassName = $_POST['modelClassName'];

            // Handle post request
            $$modelClassName->handlePost($request);
        }

        // Render view
        return self::render('test/test', $params);
    }

    public function flashTest(): void
    {
        // Set flash message
        Application::app()->session()->setFlash($_GET['type'], 'Testing flash message.');

        // Redirect to test page
        Application::app()->response()->redirect('/test');
    }

    public function error(): string
    {
        // This is for bad requests
        $exception = new BadRequestException();
        ExceptionHandler::client(message: $exception->getMessage(), exception: $exception);
    }

    public function errorTest(): void
    {
        // This is for testing exceptions

        // Throw exception
        //throw new AppException(message: "Test error message.");

        // Throw exception and include previous exception
        $exception = new Exception(message: "Original exception message here.");
        throw new AppException(message: "Test error message.", previous: $exception);

        // SQL failure
        // The Application class will catch this
        //$sql = "SELECT * FROM users2 WHERE id = 1";
        //$statement = Application::app()->db()->prepare($sql);
        //$statement->execute();
    }
}
