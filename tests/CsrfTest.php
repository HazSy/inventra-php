<?php

declare(strict_types=1);

namespace Tests;

use App\Csrf;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    #[Test]
    public function genera_un_token_de_64_caracteres_hexadecimales(): void
    {
        $token = Csrf::token();

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
    }

    #[Test]
    public function reutiliza_el_mismo_token_durante_la_sesion(): void
    {
        self::assertSame(Csrf::token(), Csrf::token());
    }

    #[Test]
    public function acepta_el_token_correcto(): void
    {
        $token = Csrf::token();

        self::assertTrue(Csrf::check($token));
    }

    #[Test]
    public function rechaza_tokens_incorrectos_o_vacios(): void
    {
        Csrf::token();

        self::assertFalse(Csrf::check('token-falso'));
        self::assertFalse(Csrf::check(''));
        self::assertFalse(Csrf::check(null));
    }

    #[Test]
    public function rechaza_cualquier_token_si_no_hay_sesion(): void
    {
        self::assertFalse(Csrf::check('cualquiera'));
    }

    #[Test]
    public function el_campo_oculto_escapa_el_valor(): void
    {
        $field = Csrf::field();

        self::assertStringContainsString('type="hidden"', $field);
        self::assertStringContainsString('name="_csrf"', $field);
        self::assertStringContainsString(Csrf::token(), $field);
    }

    #[Test]
    public function rotar_cambia_el_token(): void
    {
        $original = Csrf::token();
        Csrf::rotate();
        $nuevo = Csrf::token();

        self::assertNotSame($original, $nuevo);
        self::assertFalse(Csrf::check($original));
        self::assertTrue(Csrf::check($nuevo));
    }
}
