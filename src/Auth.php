<?php

declare(strict_types=1);

namespace App;

/**
 * Autenticación con sesión y contraseñas hasheadas con `password_hash` (Argon2id
 * si está disponible; bcrypt como alternativa).
 */
final class Auth
{
    private const SESSION_KEY = 'user_id';

    public static function hash(string $password): string
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;

        return password_hash($password, $algo);
    }

    public static function attempt(string $email, string $password): bool
    {
        $statement = Database::pdo()->prepare(
            'SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1'
        );
        $statement->execute(['email' => mb_strtolower(trim($email))]);
        $user = $statement->fetch();

        if ($user === false || !password_verify($password, $user['password'])) {
            return false;
        }

        // Rehash automático si el coste o el algoritmo han cambiado.
        if (password_needs_rehash($user['password'], defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT)) {
            $update = Database::pdo()->prepare('UPDATE users SET password = :hash WHERE id = :id');
            $update->execute(['hash' => self::hash($password), 'id' => $user['id']]);
        }

        self::login((int) $user['id']);

        return true;
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $userId;
        Csrf::rotate();
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public static function id(): ?int
    {
        $id = $_SESSION[self::SESSION_KEY] ?? null;

        return $id !== null ? (int) $id : null;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        $statement = Database::pdo()->prepare(
            'SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1'
        );
        $statement->execute(['id' => self::id()]);
        $user = $statement->fetch();

        return $user === false ? null : $user;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
