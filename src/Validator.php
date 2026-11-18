<?php

declare(strict_types=1);

namespace App;

/**
 * Validación declarativa de formularios.
 *
 *   $validator = Validator::make($_POST, [
 *       'name'     => 'required|min:2|max:120',
 *       'email'    => 'required|email',
 *       'price'    => 'required|numeric|min:0',
 *       'stock'    => 'required|integer|min:0',
 *   ]);
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $data;

    /** @var array<string, string> */
    private array $labels;

    private function __construct(array $data, private readonly array $rules, array $labels = [])
    {
        $this->data = $data;
        $this->labels = $labels;
    }

    public static function make(array $data, array $rules, array $labels = []): self
    {
        $validator = new self($data, $rules, $labels);
        $validator->validate();

        return $validator;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return !$this->fails();
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** Devuelve solo los campos validados (lista blanca). */
    public function validated(): array
    {
        $result = [];

        foreach (array_keys($this->rules) as $field) {
            $result[$field] = $this->data[$field] ?? null;
        }

        return $result;
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $label = $this->labels[$field] ?? $field;

            foreach (explode('|', $ruleString) as $rule) {
                [$name, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

                $message = match ($name) {
                    'required' => $this->checkRequired($value) ? null : "El campo {$label} es obligatorio.",
                    'email'    => $this->checkEmail($value) ? null : "El campo {$label} no es un email válido.",
                    'numeric'  => $value === null || $value === '' || is_numeric($value) ? null : "El campo {$label} debe ser numérico.",
                    'integer'  => $value === null || $value === '' || filter_var($value, FILTER_VALIDATE_INT) !== false ? null : "El campo {$label} debe ser un número entero.",
                    'min'      => $this->checkMin($value, (float) $parameter) ? null : "El campo {$label} debe tener al menos {$parameter}.",
                    'max'      => $this->checkMax($value, (float) $parameter) ? null : "El campo {$label} no puede superar {$parameter}.",
                    'in'       => in_array((string) $value, explode(',', (string) $parameter), true) ? null : "El valor de {$label} no es válido.",
                    default    => null,
                };

                if ($message !== null) {
                    $this->errors[$field] = $message;
                    break; // un error por campo es suficiente para el usuario
                }
            }
        }
    }

    private function checkRequired(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    private function checkEmail(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true; // `required` se encarga de la obligatoriedad
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function checkMin(mixed $value, float $min): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_numeric($value)) {
            return (float) $value >= $min;
        }
        return mb_strlen((string) $value) >= $min;
    }

    private function checkMax(mixed $value, float $max): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_numeric($value)) {
            return (float) $value <= $max;
        }
        return mb_strlen((string) $value) <= $max;
    }
}
