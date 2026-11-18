<?php

declare(strict_types=1);

/**
 * Helpers globales de plantillas.
 */

if (!function_exists('e')) {
    /** Escapa cualquier valor para HTML. Se usa en TODAS las salidas. */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return \App\Csrf::field();
    }
}

if (!function_exists('money')) {
    function money(float|string $value): string
    {
        return number_format((float) $value, 2, ',', '.') . ' €';
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('old')) {
    /** Recupera el valor anterior de un campo tras un error de validación. */
    function old(string $field, array $old = []): string
    {
        return (string) ($old[$field] ?? '');
    }
}

if (!function_exists('flash')) {
    function flash(?string $type = null, ?string $message = null): ?array
    {
        if ($type !== null) {
            $_SESSION['_flash'] = ['type' => $type, 'message' => $message];
            return null;
        }

        $flash = $_SESSION['_flash'] ?? null;
        unset($_SESSION['_flash']);

        return $flash;
    }
}
