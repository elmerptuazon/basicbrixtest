<?php

namespace App\Repositories\Cart;

use App\Repositories\Repository;
use App\Models\Cart;
use DB;

class CartRepository extends Repository implements ICartRepository
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function updateCartTotalByUserID(int $user_id, int $total): void
    {
        $data = $this->model->where("user_id", $user_id)->update(["total"=>$total]);
    }

    public function checkUserHasCart(int $user_id)
    {
        $data = $this->model->where("user_id", $user_id)->latest('id')->first();

        return $data;
    }

    public function listByUserID(int $user_id)
    {
        $data = $this->model->where("user_id", $user_id)->get();

        return $data;
    }
}
