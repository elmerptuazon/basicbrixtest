<?php

namespace App\Services\Product;

use Illuminate\Support\Str;
use App\Repositories\Product\IProductRepository;

class ProductService implements IProductService
{
    private IProductRepository $productRepository;

    public function __construct(IProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function store(array $data): object {
        $product = $this->productRepository->create($data);

        return $product;
    }

    public function list(string $sortColumn, string $sortOrder): object {
        $products = $this->productRepository->paginateAll($sortColumn, $sortOrder);

        return $products;
    }

}
