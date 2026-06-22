<?php

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $requestUri): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
        $path = $this->normalize($path);

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            $this->renderNotFound();
            return;
        }

        call_user_func($this->routes[$method][$path]);
    }

    private function normalize(string $path): string
    {
        $path = trim($path, '/');
        return $path === 'index.php' ? '' : $path;
    }

    private function renderNotFound(): void
    {
        $title = 'Sayfa Bulunamadı | Tarlam Takipte';
        $content = '<main class="page page--center"><section class="panel panel--sm">'
            . '<h1 class="title">Sayfa Bulunamadı</h1>'
            . '<p class="subtitle">Aradığın sayfa mevcut değil.</p>'
            . '<a class="button button--block" href="/login">Giriş Sayfasına Dön</a>'
            . '</section></main>';

        require __DIR__ . '/../Views/layout.php';
    }
}
