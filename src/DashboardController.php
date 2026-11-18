<?php

declare(strict_types=1);

namespace App;

final class DashboardController extends Controller
{
    public function __construct(private readonly ProductRepository $products)
    {
    }

    public function index(): void
    {
        $this->guard();

        $this->view('dashboard', [
            'title'      => 'Panel',
            'user'       => Auth::user(),
            'stats'      => $this->products->stats(),
            'lowStock'   => $this->products->lowStock(),
            'recent'     => $this->products->paginate('', 1, 5)['items'],
        ]);
    }
}
