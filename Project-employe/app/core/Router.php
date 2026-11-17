<?php

class Router
{
    private array $routes = [
        'GET'  => [],
        'POST' => [],
    ];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        if (BASE_URL !== '' && str_starts_with($uriPath, BASE_URL)) {
            $uriPath = substr($uriPath, strlen(BASE_URL));
        }

        if ($uriPath === '' || $uriPath === false) {
            $uriPath = '/';
        }

        if (!isset($this->routes[$method][$uriPath])) {
            http_response_code(404);
            echo '<h1>404 - Halaman Tidak Ditemukan</h1>';
            return;
        }

        [$controllerName, $action] = $this->routes[$method][$uriPath];
        $controller = new $controllerName();
        $controller->$action();
    }
}