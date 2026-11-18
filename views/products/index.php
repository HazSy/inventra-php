<?php /** @var array $items */ /** @var string $search */ /** @var int $total */ /** @var int $pages */ /** @var int $page */ ?>
<div class="page-head">
    <div>
        <h1>Productos</h1>
        <p class="muted"><?= (int) $total ?> resultados<?= $search !== '' ? ' para «' . e($search) . '»' : '' ?></p>
    </div>
    <a class="button" href="/productos/nuevo">+ Nuevo producto</a>
</div>

<form class="search" method="get" action="/productos" role="search">
    <label class="sr-only" for="q">Buscar</label>
    <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Buscar por nombre, SKU o categoría…">
    <button type="submit" class="button button--ghost">Buscar</button>
    <?php if ($search !== ''): ?>
        <a href="/productos" class="small">Limpiar</a>
    <?php endif; ?>
</form>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="right">Precio</th>
                    <th class="right">Stock</th>
                    <th class="right">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($items as $product): ?>
                <?php $low = (int) $product['stock'] <= (int) $product['min_stock']; ?>
                <tr>
                    <td>
                        <strong><?= e($product['name']) ?></strong>
                        <span class="muted small"><?= e($product['sku']) ?></span>
                    </td>
                    <td><?= e($product['category']) ?></td>
                    <td class="right"><?= money($product['price']) ?></td>
                    <td class="right">
                        <span class="badge <?= $low ? 'badge--warn' : '' ?>"><?= (int) $product['stock'] ?> u.</span>
                    </td>
                    <td class="right actions">
                        <a href="/productos/<?= (int) $product['id'] ?>/editar" class="small">Editar</a>
                        <form method="post" action="/productos/<?= (int) $product['id'] ?>/eliminar"
                              onsubmit="return confirm('¿Eliminar <?= e($product['name']) ?>?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="link link--danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($items === []): ?>
        <p class="empty">No hay productos que coincidan con la búsqueda.</p>
    <?php endif; ?>
</section>

<?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Paginación">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="/productos?page=<?= $i ?>&q=<?= urlencode($search) ?>"
               <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
