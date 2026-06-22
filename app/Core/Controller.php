<?php

class Controller
{
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function requireAuth(): array
    {
        $user = $this->currentUser();

        if (!$user) {
            $this->redirect('/login');
        }

        return $user;
    }

    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    protected function old(array $data): void
    {
        $_SESSION['old'] = $data;
    }

    protected function view(string $view, array $data = []): void
    {
        $data['flash'] = $_SESSION['flash'] ?? [];
        $data['old'] = $_SESSION['old'] ?? [];
        unset($_SESSION['flash'], $_SESSION['old']);

        extract($data, EXTR_SKIP);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            http_response_code(500);
            echo 'View bulunamadı.';
            return;
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layout.php';
    }
}
