<?php

namespace App\Repositories\Product;

use App\Repositories\Repository;
use App\Models\Product;
use DB;

class ProductRepository extends Repository implements IProductRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function searchProduct(string $keyword): object
    {
        $products = $this->model
                    ->where('name', 'LIKE', '%'.$keyword.'%')
                    ->paginate(config('services.paginate.total_page'));

        return $products;
    }

    public function updateAvailableQuantity(int $product_id, int $qty)
    {
        $this->model->where("id", $product_id)->update(["available_quantity"=>$qty]);
    }
}
