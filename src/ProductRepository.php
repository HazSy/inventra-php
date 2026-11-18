<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Acceso a datos de productos. Todas las consultas usan sentencias preparadas
 * con parámetros enlazados y tipos explícitos.
 */
final class ProductRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** @return array{items: array<int, array<string, mixed>>, total: int, pages: int, page: int} */
    public function paginate(string $search = '', int $page = 1, int $perPage = 8): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $offset = ($page - 1) * $perPage;

        $where = '';
        $params = [];
        if ($search !== '') {
            $where = 'WHERE name LIKE :search_name OR sku LIKE :search_sku OR category LIKE :search_category';
            $params['search_name'] = '%' . $search . '%';
            $params['search_sku'] = '%' . $search . '%';
            $params['search_category'] = '%' . $search . '%';
        }

        $count = $this->pdo->prepare("SELECT COUNT(*) FROM products {$where}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $statement = $this->pdo->prepare(
            "SELECT * FROM products {$where} ORDER BY name ASC LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value, PDO::PARAM_STR);
        }
        $statement->bindValue('limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return [
            'items' => $statement->fetchAll(),
            'total' => $total,
            'pages' => (int) max(1, ceil($total / $perPage)),
            'page'  => $page,
        ];
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product === false ? null : $product;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO products (sku, name, category, price, stock, min_stock)
             VALUES (:sku, :name, :category, :price, :stock, :min_stock)'
        );
        $statement->execute([
            'sku'       => $data['sku'],
            'name'      => $data['name'],
            'category'  => $data['category'],
            'price'     => $data['price'],
            'stock'     => $data['stock'],
            'min_stock' => $data['min_stock'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE products
                SET sku = :sku, name = :name, category = :category,
                    price = :price, stock = :stock, min_stock = :min_stock
              WHERE id = :id'
        );
        $statement->execute([
            'id'        => $id,
            'sku'       => $data['sku'],
            'name'      => $data['name'],
            'category'  => $data['category'],
            'price'     => $data['price'],
            'stock'     => $data['stock'],
            'min_stock' => $data['min_stock'],
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM products WHERE id = :id');

        return $statement->execute(['id' => $id]) && $statement->rowCount() > 0;
    }

    /** @return array<int, array<string, mixed>> */
    public function lowStock(): array
    {
        return $this->pdo->query('SELECT * FROM vw_low_stock ORDER BY stock ASC')->fetchAll();
    }

    /** @return array{products: int, units: int, value: float, low: int} */
    public function stats(): array
    {
        $row = $this->pdo->query(
            'SELECT COUNT(*) AS products,
                    COALESCE(SUM(stock), 0) AS units,
                    COALESCE(SUM(stock * price), 0) AS value
               FROM products'
        )->fetch();

        $low = $this->pdo->query('SELECT COUNT(*) FROM vw_low_stock')->fetchColumn();

        return [
            'products' => (int) $row['products'],
            'units'    => (int) $row['units'],
            'value'    => (float) $row['value'],
            'low'      => (int) $low,
        ];
    }
}
