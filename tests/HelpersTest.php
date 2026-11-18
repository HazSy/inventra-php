<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    #[Test]
    public function e_escapa_html_y_comillas(): void
    {
        $resultado = e('<script>alert("x")</script>');

        self::assertSame('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', $resultado);
        self::assertStringNotContainsString('<script>', $resultado);
    }

    #[Test]
    public function e_convierte_valores_nulos_y_numericos(): void
    {
        self::assertSame('', e(null));
        self::assertSame('12', e(12));
    }

    #[Test]
    public function money_formatea_en_euros_con_coma_decimal(): void
    {
        self::assertSame('1.234,50 €', money('1234.5'));
        self::assertSame('0,00 €', money(0));
    }

    #[Test]
    public function old_devuelve_el_valor_anterior_o_vacio(): void
    {
        self::assertSame('Monitor', old('name', ['name' => 'Monitor']));
        self::assertSame('', old('price', ['name' => 'Monitor']));
        self::assertSame('', old('price'));
    }
}
