<?php
namespace App\Services\Order;

use Laravel\Sanctum\NewAccessToken;

interface IOrderService {
    public function addOrder(array $data): object;
    public function addOrderDetail(int $order_id, array $data): array;
    public function searchOrder(array $key): object;
}
