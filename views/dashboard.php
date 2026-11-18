<?php /** @var array $stats */ /** @var array $lowStock */ /** @var array $recent */ ?>
<h1>Panel</h1>
<p class="muted">Resumen del inventario en tiempo real.</p>

<div class="stats">
    <article class="card">
        <p class="card__label">Productos</p>
        <p class="card__value"><?= number_format($stats['products'], 0, ',', '.') ?></p>
    </article>
    <article class="card">
        <p class="card__label">Unidades en stock</p>
        <p class="card__value"><?= number_format($stats['units'], 0, ',', '.') ?></p>
    </article>
    <article class="card">
        <p class="card__label">Valor del inventario</p>
        <p class="card__value"><?= money($stats['value']) ?></p>
    </article>
    <article class="card <?= $stats['low'] > 0 ? 'card--warn' : '' ?>">
        <p class="card__label">Bajo mínimo</p>
        <p class="card__value"><?= $stats['low'] ?></p>
    </article>
</div>

<div class="split">
    <section class="panel">
        <div class="panel__head">
            <h2>Reposición pendiente</h2>
            <a href="/productos" class="small">Ver todos</a>
        </div>

        <?php if ($lowStock === []): ?>
            <p class="muted">Todo el stock está por encima del mínimo. 👌</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Producto</th><th class="right">Stock</th><th class="right">Mínimo</th></tr>
                </thead>
                <tbody>
                <?php foreach ($lowStock as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e($item['name']) ?></strong>
                            <span class="muted small"><?= e($item['sku']) ?></span>
                        </td>
                        <td class="right badge-warn"><?= (int) $item['stock'] ?></td>
                        <td class="right"><?= (int) $item['min_stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel__head">
            <h2>Últimos productos</h2>
            <a href="/productos" class="small">Gestionar</a>
        </div>
        <ul class="list">
            <?php foreach ($recent as $product): ?>
                <li>
                    <div>
                        <strong><?= e($product['name']) ?></strong>
                        <span class="muted small"><?= e($product['category']) ?></span>
                    </div>
                    <span><?= money($product['price']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
