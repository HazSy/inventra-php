<?php

declare(strict_types=1);

namespace Tests;

use App\Validator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    #[Test]
    public function acepta_datos_correctos(): void
    {
        $validator = Validator::make(
            ['name' => 'Teclado mecánico', 'price' => '49.95', 'stock' => '12'],
            ['name' => 'required|min:2|max:120', 'price' => 'required|numeric|min:0', 'stock' => 'required|integer|min:0'],
        );

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
        self::assertSame([], $validator->errors());
    }

    #[Test]
    public function detecta_campos_obligatorios_vacios(): void
    {
        $validator = Validator::make(
            ['name' => '   ', 'sku' => null],
            ['name' => 'required', 'sku' => 'required'],
            ['sku' => 'SKU'],
        );

        self::assertTrue($validator->fails());
        self::assertSame('El campo name es obligatorio.', $validator->errors()['name']);
        self::assertSame('El campo SKU es obligatorio.', $validator->errors()['sku']);
    }

    #[Test]
    public function valida_el_formato_de_email(): void
    {
        $invalido = Validator::make(['email' => 'oscar@'], ['email' => 'required|email']);
        self::assertTrue($invalido->fails());
        self::assertSame('El campo email no es un email válido.', $invalido->errors()['email']);

        $valido = Validator::make(['email' => 'oscar@example.com'], ['email' => 'required|email']);
        self::assertTrue($valido->passes());
    }

    #[Test]
    public function el_email_vacio_no_falla_si_no_es_obligatorio(): void
    {
        $validator = Validator::make(['email' => ''], ['email' => 'email']);

        self::assertTrue($validator->passes());
    }

    #[Test]
    public function aplica_min_y_max_a_numeros(): void
    {
        $validator = Validator::make(
            ['price' => '-1', 'stock' => '1500'],
            ['price' => 'numeric|min:0', 'stock' => 'integer|max:1000'],
        );

        self::assertTrue($validator->fails());
        self::assertSame('El campo price debe tener al menos 0.', $validator->errors()['price']);
        self::assertSame('El campo stock no puede superar 1000.', $validator->errors()['stock']);
    }

    #[Test]
    public function aplica_min_y_max_a_cadenas_por_longitud(): void
    {
        $validator = Validator::make(
            ['name' => 'ab'],
            ['name' => 'min:3'],
        );
        self::assertTrue($validator->fails());

        $largo = Validator::make(['name' => 'abcd'], ['name' => 'max:3']);
        self::assertTrue($largo->fails());
    }

    #[Test]
    public function cuenta_caracteres_unicode_en_las_longitudes(): void
    {
        // "cámara" tiene 6 caracteres aunque ocupe 7 bytes en UTF-8.
        $validator = Validator::make(['name' => 'cámara'], ['name' => 'max:6']);

        self::assertTrue($validator->passes());
    }

    #[Test]
    public function rechaza_valores_fuera_de_la_lista_permitida(): void
    {
        $rules = ['role' => 'in:admin,user'];

        self::assertTrue(Validator::make(['role' => 'root'], $rules)->fails());
        self::assertTrue(Validator::make(['role' => 'admin'], $rules)->passes());
    }

    #[Test]
    public function valida_enteros_y_rechaza_decimales(): void
    {
        $validator = Validator::make(['stock' => '3.5'], ['stock' => 'integer']);

        self::assertTrue($validator->fails());
        self::assertSame('El campo stock debe ser un número entero.', $validator->errors()['stock']);
    }

    #[Test]
    public function solo_devuelve_los_campos_declarados_en_las_reglas(): void
    {
        $validator = Validator::make(
            ['name' => 'Monitor', 'price' => '120', 'is_admin' => '1'],
            ['name' => 'required', 'price' => 'required|numeric'],
        );

        self::assertSame(['name' => 'Monitor', 'price' => '120'], $validator->validated());
    }

    #[Test]
    public function guarda_un_unico_error_por_campo(): void
    {
        $validator = Validator::make(['name' => ''], ['name' => 'required|min:3|max:10']);

        self::assertCount(1, $validator->errors());
        self::assertSame('El campo name es obligatorio.', $validator->errors()['name']);
    }
}
