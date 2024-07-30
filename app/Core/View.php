<?php

namespace App\Core;

final class View
{
    public function renderView(string $view, $params = []): string
    {
        // Get page settings from the controller
        $layout = Application::app()->controller()->getProperty('page')['layout'];

        // Check if layout exists
        if (!file_exists(BASE_PATH . "/app/Views/layouts/$layout.php")) {
            throw new (Functions::getConfig("class/exception/framework"))(message: "Layout not found: $layout");
        }

        // Check if view exists
        if (!file_exists(BASE_PATH . "/app/Views/$view.php")) {
            throw new (Functions::getConfig("class/exception/framework"))(message: "View not found: $view");
        }

        // Get view variables and content
        // Do this before the layout, so we can show view errors in the layout
        $viewArray = $this->viewContent($view, $params);
        $viewVariables = $viewArray[0];
        $viewContent = $viewArray[1];

        // Get layout content
        $layoutContent = $this->layoutContent($layout);

        // Get title - default to config if not provided
        $title = $viewVariables["title"] ?? Functions::getConfig("page/default/title");

        // Replace title, padding and content placeholders
        $layoutContent = str_replace('{{title}}', $title, $layoutContent);
        $layoutContent = str_replace('{{content}}', $viewContent, $layoutContent);

        // Content length header - currently not using since Transfer-Encoding header defaults to "chunked"
        //header('Content-Length: ' . strlen($layoutContent));

        // Return layout
        return $layoutContent;
    }

    protected function layoutContent($layout): string
    {
        // Get layout - use output buffering so we parse all code
        ob_start();
        include BASE_PATH . "/app/Views/layouts/$layout.php";
        $output = ob_get_clean();

        // Return output
        return $output;
    }

    protected function viewContent($view, $params): ?array
    {
        // Get view - use output buffering so we parse all code
        ob_start();

        // Add the query array as a variable that the view can access
        $query = Application::app()->request()->getQuery();

        // Include view
        include BASE_PATH . "/app/Views/$view.php";

        // Get all variables
        $viewVariables = get_defined_vars();

        // Get contents of active buffer and turn off
        $viewArray = ob_get_clean();

        // Return variables and view
        return [$viewVariables, $viewArray];
    }
}
