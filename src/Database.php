<?php

declare(strict_types=1);

namespace App;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Conexión PDO única (patrón singleton) con configuración segura por defecto.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connect(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset'],
        );

        try {
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Preparaciones reales en el servidor: evita la emulación de PDO,
                // que es la causa habitual de inyección SQL en código "preparado".
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('No se pudo conectar a la base de datos: ' . $e->getMessage(), 0, $e);
        }

        return self::$pdo;
    }

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            throw new RuntimeException('La base de datos no está inicializada.');
        }

        return self::$pdo;
    }

    /** Útil en tests para inyectar una conexión limpia. */
    public static function setPdo(?PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}
