<?php
namespace App\Services\Product;


interface IProductService {
    public function store(array $data): object;
    public function list(string $sortColumn, string $sortOrder): object;
}
