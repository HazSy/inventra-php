<?php /** @var array $errors */ ?>
<section class="auth">
    <h1>Crear cuenta</h1>
    <p class="muted">Necesaria para administrar el inventario.</p>

    <form method="post" action="/register" novalidate>
        <?= csrf_field() ?>

        <label for="name">Nombre</label>
        <input type="text" id="name" name="name" value="<?= e($_SESSION['_old']['name'] ?? '') ?>" required autocomplete="name">
        <?php if (isset($errors['name'])): ?>
            <p class="field-error"><?= e($errors['name']) ?></p>
        <?php endif; ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($_SESSION['_old']['email'] ?? '') ?>" required autocomplete="email">
        <?php if (isset($errors['email'])): ?>
            <p class="field-error"><?= e($errors['email']) ?></p>
        <?php endif; ?>

        <label for="password">Contraseña <small class="muted">(mínimo 8 caracteres)</small></label>
        <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
        <?php if (isset($errors['password'])): ?>
            <p class="field-error"><?= e($errors['password']) ?></p>
        <?php endif; ?>

        <button type="submit" class="button">Crear cuenta</button>
    </form>

    <p class="muted small">¿Ya tienes cuenta? <a href="/login">Iniciar sesión</a></p>
</section>
