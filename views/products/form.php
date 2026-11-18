<?php
/** @var array|null $product */
/** @var array $errors */
/** @var array $old */
$isEdit = $product !== null;
$action = $isEdit ? '/productos/' . (int) $product['id'] . '/editar' : '/productos';
$value = static fn (string $field): string => e($old[$field] ?? '');
?>
<div class="page-head">
    <div>
        <h1><?= $isEdit ? 'Editar producto' : 'Nuevo producto' ?></h1>
        <p class="muted">Los campos con <span aria-hidden="true">*</span> son obligatorios.</p>
    </div>
    <a class="small" href="/productos">← Volver</a>
</div>

<form class="panel form" method="post" action="<?= $action ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form__grid">
        <div>
            <label for="sku">SKU *</label>
            <input type="text" id="sku" name="sku" value="<?= $value('sku') ?>" required maxlength="20" placeholder="TEC-001">
            <?php if (isset($errors['sku'])): ?><p class="field-error"><?= e($errors['sku']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="category">Categoría *</label>
            <input type="text" id="category" name="category" value="<?= $value('category') ?>" required maxlength="60" placeholder="Periféricos">
            <?php if (isset($errors['category'])): ?><p class="field-error"><?= e($errors['category']) ?></p><?php endif; ?>
        </div>

        <div class="form__full">
            <label for="name">Nombre *</label>
            <input type="text" id="name" name="name" value="<?= $value('name') ?>" required maxlength="120">
            <?php if (isset($errors['name'])): ?><p class="field-error"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="price">Precio (€) *</label>
            <input type="number" id="price" name="price" value="<?= $value('price') ?>" required min="0" step="0.01">
            <?php if (isset($errors['price'])): ?><p class="field-error"><?= e($errors['price']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="stock">Stock actual *</label>
            <input type="number" id="stock" name="stock" value="<?= $value('stock') ?>" required min="0" step="1">
            <?php if (isset($errors['stock'])): ?><p class="field-error"><?= e($errors['stock']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="min_stock">Stock mínimo *</label>
            <input type="number" id="min_stock" name="min_stock" value="<?= $value('min_stock') ?: '5' ?>" required min="0" step="1">
            <?php if (isset($errors['min_stock'])): ?><p class="field-error"><?= e($errors['min_stock']) ?></p><?php endif; ?>
        </div>
    </div>

    <div class="form__actions">
        <button type="submit" class="button"><?= $isEdit ? 'Guardar cambios' : 'Crear producto' ?></button>
        <a class="button button--ghost" href="/productos">Cancelar</a>
    </div>
</form>
