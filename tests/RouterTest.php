<?php

declare(strict_types=1);

namespace Tests;

use App\NotFoundException;
use App\Router;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    #[Test]
    public function resuelve_una_ruta_estatica(): void
    {
        $router = new Router();
        $router->get('/productos', static fn (): string => 'lista');

        self::assertSame('lista', $router->dispatch('GET', '/productos'));
    }

    #[Test]
    public function ignora_la_barra_final(): void
    {
        $router = new Router();
        $router->get('/productos', static fn (): string => 'lista');

        self::assertSame('lista', $router->dispatch('GET', '/productos/'));
    }

    #[Test]
    public function normaliza_la_raiz(): void
    {
        $router = new Router();
        $router->get('/', static fn (): string => 'home');

        self::assertSame('home', $router->dispatch('GET', ''));
    }

    #[Test]
    public function extrae_parametros_de_la_url(): void
    {
        $router = new Router();
        $router->get('/productos/{id}/editar', static fn (array $params): string => 'editar-' . $params['id']);

        self::assertSame('editar-42', $router->dispatch('GET', '/productos/42/editar'));
    }

    #[Test]
    public function distingue_metodos_http(): void
    {
        $router = new Router();
        $router->get('/login', static fn (): string => 'form');
        $router->post('/login', static fn (): string => 'submit');

        self::assertSame('form', $router->dispatch('GET', '/login'));
        self::assertSame('submit', $router->dispatch('post', '/login'));
    }

    #[Test]
    public function ignora_la_query_string(): void
    {
        $router = new Router();
        $router->get('/productos', static fn (): string => 'lista');

        self::assertSame('lista', $router->dispatch('GET', '/productos?page=2&q=monitor'));
    }

    #[Test]
    public function devuelve_el_resultado_del_handler(): void
    {
        $router = new Router();
        $router->get('/numero', static fn (): int => 7);

        self::assertSame(7, $router->dispatch('GET', '/numero'));
    }

    #[Test]
    public function lanza_excepcion_si_no_hay_ruta(): void
    {
        $router = new Router();
        $router->get('/productos', static fn (): string => 'lista');

        $this->expectException(NotFoundException::class);
        $router->dispatch('GET', '/no-existe');
    }

    #[Test]
    public function lanza_excepcion_si_el_metodo_no_coincide(): void
    {
        $router = new Router();
        $router->post('/productos', static fn (): string => 'creado');

        $this->expectException(NotFoundException::class);
        $router->dispatch('GET', '/productos');
    }
}
