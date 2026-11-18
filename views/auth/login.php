<?php /** @var array $errors */ ?>
<section class="auth">
    <h1>Iniciar sesión</h1>
    <p class="muted">Accede para gestionar el inventario.</p>

    <form method="post" action="/login" novalidate>
        <?= csrf_field() ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($_SESSION['_old']['email'] ?? '') ?>" required autocomplete="email">
        <?php if (isset($errors['email'])): ?>
            <p class="field-error"><?= e($errors['email']) ?></p>
        <?php endif; ?>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
        <?php if (isset($errors['password'])): ?>
            <p class="field-error"><?= e($errors['password']) ?></p>
        <?php endif; ?>

        <button type="submit" class="button">Entrar</button>
    </form>

    <p class="muted small">¿No tienes cuenta? <a href="/register">Crear una</a></p>
    <p class="muted small">Demo: <code>demo@inventra.dev</code> / <code>demo1234</code></p>
</section>
