# Inventra · Gestor de inventario

Aplicación web de gestión de inventario para pequeñas empresas: catálogo de productos,
alertas de stock mínimo, histórico automático de movimientos y panel de métricas.

Construida con **PHP 8.4 + MySQL + PDO** y arquitectura **MVC propia, sin frameworks**:
router, contenedor de dependencias manual, validación declarativa, capa de acceso a datos
y plantillas PHP con escapado obligatorio.

![CI](https://github.com/HazSy/inventra-php/actions/workflows/ci.yml/badge.svg)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green)

---

## Demo

> La demo pública requiere hosting con PHP y MySQL. Para probarlo en local sigue los
> pasos de [Puesta en marcha](#puesta-en-marcha): la base de datos incluye datos de ejemplo.

**Credenciales de ejemplo:** `demo@inventra.dev` / `demo1234`

### Capturas

| Panel | Productos |
| --- | --- |
| _Añade aquí una captura del dashboard_ | _Añade aquí una captura del listado_ |

---

## Funcionalidades

- **Autenticación** con sesiones seguras, `password_hash` (Argon2id si está disponible) y
  rehash automático cuando cambia el algoritmo o el coste.
- **CRUD de productos** con búsqueda por nombre, SKU o categoría y paginación en servidor.
- **Alertas de stock mínimo** mediante una vista SQL (`vw_low_stock`).
- **Histórico de movimientos** generado por un trigger de MySQL en cada cambio de stock.
- **Métricas del inventario**: número de referencias, unidades totales, valor del stock y
  productos bajo mínimo.
- **Seguridad**: tokens CSRF por sesión, cookies `HttpOnly` + `SameSite=Lax`, sentencias
  preparadas con tipos explícitos, escapado HTML centralizado y cabeceras CSP/X-Frame-Options.
- **Diseño responsive** en CSS puro, con tema claro/oscuro automático.

## Stack

| Capa | Tecnología |
| --- | --- |
| Lenguaje | PHP 8.4 (compatible con 8.2+) |
| Base de datos | MySQL 8 / MariaDB 10.4+ |
| Acceso a datos | PDO con sentencias preparadas |
| Frontend | HTML semántico + CSS moderno (sin frameworks) |
| Tests | PHPUnit 11 |
| Calidad | `php -l`, GitHub Actions |

## Arquitectura

```
public/                  único punto de entrada (document root)
  index.php              bootstrap: sesión, cabeceras, rutas
  assets/app.css
config/config.php        configuración por variables de entorno
src/
  Router.php             rutas con parámetros `{id}`
  Database.php           conexión PDO singleton
  Auth.php               login, hash y sesión
  Csrf.php               token CSRF
  Validator.php          reglas declarativas (`required|min:2|max:120`)
  View.php               render de plantillas con layout
  ProductRepository.php  consultas SQL del dominio
  *Controller.php        controladores (dashboard, productos, auth)
views/                   plantillas PHP
database/
  schema.sql             tablas, vista y trigger
  seed.sql               usuario y 10 productos de ejemplo
tests/                   PHPUnit (validación, router, CSRF, helpers)
```

Flujo de una petición:

```
HTTP → public/index.php → Router → Controller → Repository (PDO) → View → HTML
```

La lógica de negocio no conoce HTTP y el acceso a datos no conoce HTML: cada capa se puede
probar o sustituir por separado.

## Puesta en marcha

### Requisitos

- PHP 8.2 o superior con `pdo_mysql` y `mbstring`
- MySQL 8 o MariaDB 10.4+
- Composer 2

### Pasos

```bash
git clone https://github.com/HazSy/inventra-php.git
cd inventra-php
composer install

# 1. Crear la base de datos, la vista y el trigger
mysql -u root -p < database/schema.sql

# 2. Cargar los datos de ejemplo (opcional)
mysql -u root -p inventra < database/seed.sql

# 3. Configurar credenciales si no son las de XAMPP por defecto
export DB_HOST=127.0.0.1
export DB_PORT=3306
export DB_NAME=inventra
export DB_USER=root
export DB_PASSWORD=

# 4. Arrancar el servidor de desarrollo
composer serve          # http://localhost:8000
```

Con XAMPP también puedes copiar el proyecto a `htdocs` y apuntar el VirtualHost a
`public/`; nunca expongas la raíz del proyecto, porque `config/` y `vendor/` quedarían
accesibles.

## Tests y calidad

```bash
composer test                                      # PHPUnit
find src public tests -name '*.php' -exec php -l {} \;   # sintaxis
```

La integración continua (`.github/workflows/ci.yml`) ejecuta la suite con PHP 8.2, 8.3 y 8.4
en cada push y pull request.

## Decisiones técnicas

- **Sin framework**: el objetivo es demostrar comprensión de HTTP, enrutado, sesiones y SQL,
  no el uso de un ORM. El coste es más código propio; el beneficio es control total y un
  binario de dependencias mínimo (solo PHPUnit en desarrollo).
- **PDO con parámetros enlazados y tipos**: evita inyección SQL y conversiones implícitas.
- **Vista SQL para el stock bajo**: la regla `stock <= min_stock` vive en un único sitio y
  se reutiliza en el dashboard y el listado.
- **Trigger de auditoría**: los movimientos se registran aunque el cambio venga de fuera de
  la aplicación (por ejemplo, un script de importación).
- **Escapado en la capa de vista**: `e()` se aplica a toda salida dinámica; el escapado no
  depende de que cada controlador se acuerde.

## Posibles mejoras

- API JSON para integraciones y app móvil.
- Exportación de inventario a CSV/PDF.
- Roles (administrador / operador) y auditoría de accesos.
- Tests de integración con una base de datos efímera en CI.

## Licencia

MIT © 2026 Óscar López Pellicer
