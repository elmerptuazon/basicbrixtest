<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Order\IOrderService;
use App\Services\Responses\IResponseService;
use App\Http\Requests\Order\OrderRequest;
use App\Enums\SuccessMessages;
use App\Repositories\Order\IOrderRepository;
use App\Models\Order;
use App\Http\Requests\Order\SearchOrderRequest;

class OrderController extends Controller
{

    private IOrderService $orderService;
    private IResponseService $responseService;
    private IOrderRepository $orderRepository;
    
    public function __construct(IOrderService $orderService,
                                IResponseService $responseService,
                                IOrderRepository $orderRepository)
    {
        $this->orderService = $orderService;
        $this->responseService = $responseService;
        $this->orderRepository = $orderRepository;
    }

    public function store(OrderRequest $request) {
        $data = $request->validated();
        $order_data = array_slice($data, 0, 3);
        $addOrder = $this->orderService->addOrder($order_data);
        $total_order = $this->orderService->addOrderDetail($addOrder->id, $data);
        $order_data["gross_sales"] = array_sum($total_order);
        $this->orderRepository->update($addOrder, $order_data);

        return $this->responseService->successResponse($addOrder->toArray(), SuccessMessages::order_added);
    }

    public function update(OrderRequest $request, Order $order_id) {
        $data = $request->validated();
        $updateRecord = $this->orderRepository->update($order_id, $data);

        return $this->responseService->successResponse(array($updateRecord), SuccessMessages::order_updated);
    }

    public function search(SearchOrderRequest $request) {
        $data = $request->validated();
        $result = $this->orderService->searchOrder($data);

        return $this->responseService->successResponse(array($result), SuccessMessages::success);
    }
}
