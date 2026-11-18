<?php

declare(strict_types=1);

namespace App;

abstract class Controller
{
    protected function view(string $view, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        echo View::render($view, $data);
    }

    /** Vuelve al formulario conservando los errores y los valores enviados. */
    protected function back(string $path, array $errors = [], array $old = []): never
    {
        $_SESSION['_errors'] = $errors;
        $_SESSION['_old'] = $old;
        redirect($path);
    }

    protected function guard(): void
    {
        if (!Auth::check()) {
            redirect('/login');
        }
    }

    protected function requireCsrf(): void
    {
        if (!Csrf::check($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('Token CSRF inválido o caducado. Recarga la página e inténtalo de nuevo.');
        }
    }
}
