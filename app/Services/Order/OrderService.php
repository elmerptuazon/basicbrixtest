<?php

namespace App\Services\Order;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use App\Repositories\Order\IOrderRepository;
use App\Repositories\Order_Detail\IOrder_DetailRepository;
use App\Repositories\Product\IProductRepository;
use App\Repositories\Customer\ICustomerRepository;

class OrderService implements IOrderService
{
    private IOrderRepository $orderRepository;
    private IOrder_DetailRepository $order_detailRepository;
    private IProductRepository $productRepository;
    private ICustomerRepository $customerRepository;

    public function __construct(IOrderRepository $orderRepository,
                                IOrder_DetailRepository $order_detailRepository,
                                IProductRepository $productRepository,
                                ICustomerRepository $customerRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->order_detailRepository = $order_detailRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    public function addOrder(array $data): object {
        $order = $this->orderRepository->create($data);

        return $order;
    }

    public function addOrderDetail(int $order_id, array $data): array {
        $order_total = [];
        foreach($data["order_details"] as $val) {
            $totalPerOrder = $this->computeProductPricePerQuantity($val);
            array_push($order_total, $totalPerOrder);
            $val["gross_sales"] = $totalPerOrder;
            $body = $this->orderDetailBody($order_id, $val);
            $this->order_detailRepository->create($body);
        }

        return $order_total;
    }

    public function searchOrder(array $key): object {
        $order = $this->orderRepository->get($key["key"]);

        return (!$order) ? $this->getOrderFromCustomerName($key["key"]) : $order;
    }

    private function orderDetailBody(int $order_id, array $data): array {
        $arr = [
            "order_number" => $order_id,
            "product_code" => $data["product_code"],
            "quantity" => $data["quantity"],
            "gross_sales" => $data["gross_sales"]
        ];

        return $arr;
    }

    private function computeProductPricePerQuantity(array $data): float {
        $product = $this->productRepository->getByCode($data["product_code"]);

        $result = $data["quantity"] * $product->price;

        return $result;
    }

    private function getOrderFromCustomerName(string $name): object {
        $getCustomer = $this->customerRepository->getByName($name);
        if(!$getCustomer) abort(404, "Search key do not exists.");
        $getOrder = $this->orderRepository->getByCustomerCode($getCustomer->code);

        return $getOrder;
    }
}
