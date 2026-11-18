<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * Renderizado de vistas PHP con layout compartido.
 */
final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layout'): string
    {
        $viewFile = dirname(__DIR__) . '/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new RuntimeException("Vista no encontrada: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        if ($layout === '') {
            return $content;
        }

        $layoutFile = dirname(__DIR__) . '/views/' . $layout . '.php';
        if (!is_file($layoutFile)) {
            throw new RuntimeException("Layout no encontrado: {$layout}");
        }

        ob_start();
        require $layoutFile;

        return (string) ob_get_clean();
    }

    public static function json(mixed $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
