<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function __construct(protected Request $request, protected Response $response)
    {
    }

    public function get($path, $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        // Get path
        $path = $this->request->getPath();

        // Get method
        $method = $this->request->getMethod();

        // Save referer in session - only for get methods
        if ( $method == "get") {
            session()->setReferer();
        }

        // Determine callback
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            // We need the default controller and default not found class
            $controllerClass = getConfig("class/exception/controller");
            $notFoundException = getConfig("class/exception/notFound");

            // Create controller
            app()->setProperty('controller', new $controllerClass());

            // Show error
            $exception = new $notFoundException();
            ExceptionHandler::client(message: $exception->getMessage(), exception: $exception);
        }

        // Check callback
        if (is_array($callback)) {
            // Route has a controller and action (method in the controller)
            /** @var Controller $controller */
            /** @var Middleware $middleware */

            // Create controller instance
            $controller = new $callback[0]();

            // Update controller property in the application
            app()->setProperty('controller', $controller);

            // Set action property to the method to be run in the relevant controller
            $controller->setProperty('action', $callback[1]);

            // Update first array element to controller instance
            $callback[0] = $controller;
        } else {
            // Route is a view

            // Get controller and method for handling generic views
            $controllerClass = getConfig("controller/genericView/controller");
            $controllerMethod = getConfig("controller/genericView/method");

            // Create controller
            $controller = new $controllerClass();
            app()->setProperty('controller', $controller);

            // Set action and view
            $controller->setProperty('action', $controllerMethod);
            $controller->setProperty('view', $callback); // set view to be the callback

            // Set callback array
            $callback = [$controller, $controllerMethod];
        }

        // Apply middleware
        foreach ($controller->getMiddleware() as $middleware) {
            $middleware->execute();
        }

        // Call user function
        return call_user_func($callback, $this->request);
    }
}
