<?php

declare(strict_types=1);

namespace App;

use PDOException;

final class ProductController extends Controller
{
    private const RULES = [
        'sku'       => 'required|min:3|max:20',
        'name'      => 'required|min:2|max:120',
        'category'  => 'required|max:60',
        'price'     => 'required|numeric|min:0|max:999999',
        'stock'     => 'required|integer|min:0|max:1000000',
        'min_stock' => 'required|integer|min:0|max:1000000',
    ];

    private const LABELS = [
        'sku'       => 'SKU',
        'name'      => 'nombre',
        'category'  => 'categoría',
        'price'     => 'precio',
        'stock'     => 'stock',
        'min_stock' => 'stock mínimo',
    ];

    public function __construct(private readonly ProductRepository $products)
    {
    }

    public function index(): void
    {
        $this->guard();

        $search = trim((string) ($_GET['q'] ?? ''));
        $page = (int) ($_GET['page'] ?? 1);
        $result = $this->products->paginate($search, $page);

        $this->view('products/index', [
            'title'    => 'Productos',
            'user'     => Auth::user(),
            'search'   => $search,
            'items'    => $result['items'],
            'total'    => $result['total'],
            'pages'    => $result['pages'],
            'page'     => $result['page'],
            'errors'   => $_SESSION['_errors'] ?? [],
            'old'      => $_SESSION['_old'] ?? [],
        ]);

        unset($_SESSION['_errors'], $_SESSION['_old']);
    }

    public function create(): void
    {
        $this->guard();
        $this->view('products/form', [
            'title'  => 'Nuevo producto',
            'user'   => Auth::user(),
            'product' => null,
            'errors' => $_SESSION['_errors'] ?? [],
            'old'    => $_SESSION['_old'] ?? [],
        ]);
        unset($_SESSION['_errors'], $_SESSION['_old']);
    }

    public function store(): void
    {
        $this->guard();
        $this->requireCsrf();

        $validator = Validator::make($_POST, self::RULES, self::LABELS);
        if ($validator->fails()) {
            $this->back('/productos/nuevo', $validator->errors(), $_POST);
        }

        try {
            $this->products->create($validator->validated());
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->back('/productos/nuevo', ['sku' => 'Ese SKU ya existe.'], $_POST);
            }
            throw $e;
        }

        flash('success', 'Producto creado correctamente.');
        redirect('/productos');
    }

    public function edit(string $id): void
    {
        $this->guard();
        $product = $this->products->find((int) $id);

        if ($product === null) {
            $this->view('errors/404', ['title' => 'Producto no encontrado'], 404);
            return;
        }

        $this->view('products/form', [
            'title'   => 'Editar producto',
            'user'    => Auth::user(),
            'product' => $product,
            'errors'  => $_SESSION['_errors'] ?? [],
            'old'     => $_SESSION['_old'] ?? $product,
        ]);
        unset($_SESSION['_errors'], $_SESSION['_old']);
    }

    public function update(string $id): void
    {
        $this->guard();
        $this->requireCsrf();

        $product = $this->products->find((int) $id);
        if ($product === null) {
            $this->view('errors/404', ['title' => 'Producto no encontrado'], 404);
            return;
        }

        $validator = Validator::make($_POST, self::RULES, self::LABELS);
        if ($validator->fails()) {
            $this->back('/productos/' . $id . '/editar', $validator->errors(), $_POST);
        }

        try {
            $this->products->update((int) $id, $validator->validated());
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->back('/productos/' . $id . '/editar', ['sku' => 'Ese SKU ya existe.'], $_POST);
            }
            throw $e;
        }

        flash('success', 'Producto actualizado.');
        redirect('/productos');
    }

    public function destroy(string $id): void
    {
        $this->guard();
        $this->requireCsrf();

        $deleted = $this->products->delete((int) $id);
        flash($deleted ? 'success' : 'error', $deleted ? 'Producto eliminado.' : 'El producto no existía.');

        redirect('/productos');
    }
}
