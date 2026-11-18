<?php /** @var string $content */ /** @var string|null $title */ /** @var array|null $user */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Inventra') ?> · Inventra</title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
<?php if (!empty($user)): ?>
    <header class="topbar">
        <a class="brand" href="/">
            <span class="brand__mark" aria-hidden="true">I</span>
            <span>Inventra</span>
        </a>

        <nav aria-label="Principal">
            <a href="/" <?= ($_SERVER['REQUEST_URI'] ?? '/') === '/' ? 'aria-current="page"' : '' ?>>Panel</a>
            <a href="/productos" <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/productos') ? 'aria-current="page"' : '' ?>>Productos</a>
            <a class="button button--ghost" href="/productos/nuevo">+ Nuevo producto</a>
        </nav>

        <div class="topbar__user">
            <span><?= e($user['name']) ?></span>
            <form method="post" action="/logout">
                <?= csrf_field() ?>
                <button type="submit" class="link">Salir</button>
            </form>
        </div>
    </header>
<?php endif; ?>

<main class="container">
    <?php if ($flash = flash()): ?>
        <div class="alert alert--<?= e($flash['type']) ?>" role="status">
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="footer">
    <p>Inventra · gestor de inventario con PHP 8, MySQL y PDO · <?= date('Y') ?></p>
</footer>
</body>
</html>
