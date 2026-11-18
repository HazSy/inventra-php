<?php

declare(strict_types=1);

use App\AuthController;
use App\Database;
use App\DashboardController;
use App\NotFoundException;
use App\ProductController;
use App\ProductRepository;
use App\Router;
use App\View;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/src/helpers.php';

$config = require dirname(__DIR__) . '/config/config.php';

// --- Sesión segura -------------------------------------------------------
session_name($config['session']['name']);
session_set_cookie_params([
    'lifetime' => $config['session']['lifetime'],
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

// --- Cabeceras de seguridad ---------------------------------------------
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'");

// --- Dependencias --------------------------------------------------------
Database::connect($config['db']);
$products = new ProductRepository(Database::pdo());

$dashboard = new DashboardController($products);
$productController = new ProductController($products);
$auth = new AuthController();

// --- Rutas ---------------------------------------------------------------
$router = new Router();

$router->get('/', static fn () => $dashboard->index());

$router->get('/login', static fn () => $auth->showLogin());
$router->post('/login', static fn () => $auth->login());
$router->get('/register', static fn () => $auth->showRegister());
$router->post('/register', static fn () => $auth->register());
$router->post('/logout', static fn () => $auth->logout());

$router->get('/productos', static fn () => $productController->index());
$router->get('/productos/nuevo', static fn () => $productController->create());
$router->post('/productos', static fn () => $productController->store());
$router->get('/productos/{id}/editar', static fn (array $p) => $productController->edit($p['id']));
$router->post('/productos/{id}/editar', static fn (array $p) => $productController->update($p['id']));
$router->post('/productos/{id}/eliminar', static fn (array $p) => $productController->destroy($p['id']));

// --- Ejecución -----------------------------------------------------------
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (NotFoundException) {
    http_response_code(404);
    echo View::render('errors/404', ['title' => 'Página no encontrada'], '');
} catch (Throwable $e) {
    // Nunca se muestran detalles internos al usuario: se registran y se
    // devuelve una página genérica con un código 500.
    error_log(sprintf('[inventra] %s: %s en %s:%d', $e::class, $e->getMessage(), $e->getFile(), $e->getLine()));
    http_response_code(500);
    echo View::render('errors/500', ['title' => 'Error interno'], '');
}
