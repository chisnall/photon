<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Traits\GetSetProperty;

abstract class Controller
{
    use GetSetProperty;

    protected ?array $page = null;
    protected ?string $action = null;
    protected ?string $view = null;
    protected array $middleware = [];

    public function __construct()
    {
        // Set the default page layout
        $this->page['layout'] = Functions::getConfig("page/default/layout");
        $this->page['view'] = $this->view;
    }

    public function render(string $view, array $params = []): string
    {
        // Save view
        $this->page['view'] = $view;

        // Render view
        return Application::app()->view()->renderView($view, $params);
    }

    public function setLayout(string $layout): void
    {
        $this->page['layout'] = $layout;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function registerMiddleware(Middleware $middleware): void
    {
        $this->middleware[] = $middleware;
    }
}
