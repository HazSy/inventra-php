<?php

declare(strict_types=1);

namespace App;

use Closure;

/**
 * Router mínimo: convierte `/productos/{id}` en una expresión regular y
 * extrae los parámetros de la URL.
 */
final class Router
{
    /** @var array<int, array{method: string, pattern: string, handler: Closure}> */
    private array $routes = [];

    public function get(string $pattern, Closure $handler): self
    {
        return $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, Closure $handler): self
    {
        return $this->add('POST', $pattern, $handler);
    }

    public function add(string $method, string $pattern, Closure $handler): self
    {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];

        return $this;
    }

    /**
     * @return mixed Resultado del handler, o null si no hay coincidencia.
     */
    public function dispatch(string $method, string $uri): mixed
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $regex = $this->toRegex($route['pattern']);
            if (preg_match($regex, $path, $matches) === 1) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return ($route['handler'])($params);
            }
        }

        throw new NotFoundException("Sin ruta para {$method} {$path}");
    }

    private function toRegex(string $pattern): string
    {
        $pattern = rtrim($pattern, '/') ?: '/';
        $escaped = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);

        return '#^' . $escaped . '$#';
    }
}
