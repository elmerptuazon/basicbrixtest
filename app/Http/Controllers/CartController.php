<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Responses\IResponseService;
use App\Enums\SuccessMessages;
use App\Services\Cart\ICartService;
use App\Http\Requests\Cart\AddProductToCartRequest;
use App\Http\Requests\Cart\RemoveProductToCartRequest;

class CartController extends Controller
{
    private ICartService $cartService;
    private IResponseService $responseService;

    public function __construct(ICartService $cartService,
                                IResponseService $responseService)
    {
        $this->cartService = $cartService;
        $this->responseService = $responseService;
    }

    public function addProductToCart(AddProductToCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->cartService->addProductToCart($data);
        return $cart;
    }

    public function removeProductToCart(RemoveProductToCartRequest $request)
    {
        $data = $request->validated();
        $cart = $this->cartService->removeProductToCart($data);
        return $cart;
    }

    public function list(Request $request) 
    {
        $items = $this->cartService->list($request->user()->id);
        return $this->responseService->successResponse($items->toArray(), SuccessMessages::success);
    }
}
