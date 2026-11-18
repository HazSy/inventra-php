<?php

declare(strict_types=1);

/**
 * Configuración de la aplicación.
 * Los valores se pueden sobrescribir con variables de entorno (.env o del servidor).
 */
return [
    'app' => [
        'name' => 'Inventra',
        'env'  => getenv('APP_ENV') ?: 'local',
        'url'  => getenv('APP_URL') ?: 'http://localhost:8000',
    ],
    'db' => [
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => (int) (getenv('DB_PORT') ?: 3306),
        'database' => getenv('DB_NAME') ?: 'inventra',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset'  => 'utf8mb4',
    ],
    'session' => [
        'name'     => 'inventra_session',
        'lifetime' => 7200,
    ],
];
