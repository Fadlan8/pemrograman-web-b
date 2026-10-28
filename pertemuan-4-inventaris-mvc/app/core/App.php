<?php

class App
{
    // Sengaja TANPA tipe string: properti ini awalnya berisi nama class,
    // lalu diisi objek controller (new $this->controller).
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseURL();

        if (!empty($url[0])) {
            $candidate = ucfirst($url[0]) . 'Controller';
            if (file_exists(ROOT_PATH . '/app/controllers/' . $candidate . '.php')) {
                $this->controller = $candidate;
                unset($url[0]);
            } else {
                $this->notFound();
                return;
            }
        }

        if (!empty($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                $this->notFound();
                return;
            }
        }

        $this->params = $url ? array_values($url) : [];

        $this->controller = new $this->controller;
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseURL(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return $url === '' ? [] : explode('/', $url);
        }
        return [];
    }

    private function notFound(): void
    {
        http_response_code(404);
        extract(array_merge(view_shared_data(), [
            'title' => '404 Not Found',
            'path' => '/' . ($_GET['url'] ?? ''),
        ]));
        require ROOT_PATH . '/app/views/errors/404.php';
    }
}
