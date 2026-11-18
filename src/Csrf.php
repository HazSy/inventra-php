<?php

declare(strict_types=1);

namespace App;

/**
 * Protección CSRF con token por sesión y comparación en tiempo constante.
 */
final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public static function check(?string $token): bool
    {
        $expected = $_SESSION[self::KEY] ?? '';

        return is_string($token) && $expected !== '' && hash_equals($expected, $token);
    }

    public static function field(): string
    {
        return sprintf(
            '<input type="hidden" name="_csrf" value="%s">',
            htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8'),
        );
    }

    /** Rota el token; se llama al iniciar y cerrar sesión. */
    public static function rotate(): void
    {
        unset($_SESSION[self::KEY]);
        self::token();
    }
}
