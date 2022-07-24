<?php

namespace App\Services\Cart;

use Illuminate\Support\Str;
use App\Repositories\Cart\ICartRepository;
use App\Repositories\Product\IProductRepository;
use App\Exceptions\ApiException;
use App\Enums\ErrorMessages;
use App\Enums\SuccessMessages;
use Illuminate\Http\Response;
use App\Services\Responses\IResponseService;

class CartService implements ICartService
{
    private ICartRepository $cartRepository;
    private IProductRepository $productRepository;
    private IResponseService $responseService;

    public function __construct(ICartRepository $cartRepository,
                                IProductRepository $productRepository,
                                IResponseService $responseService)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->responseService = $responseService;
    }

    public function addProductToCart(array $items)
    {
        $cartExist = $this->cartRepository->checkUserHasCart($items["user_id"]);
        $productQty = $this->productRepository->get($items["product_id"]);
        if($productQty->available_quantity == 0) {
            return $this->responseService->unavailable(ErrorMessages::productNotAvailable);
        }
        if($cartExist) {
            $totalQty = $cartExist->total + 1;
            $items["total"] = $totalQty;
            $cart = $this->cartRepository->create($items);
            $this->cartRepository->updateCartTotalByUserID($items["user_id"], $totalQty);
        } else {
            $items["total"] = 1;
            $cart = $this->cartRepository->create($items);
        }
        $this->productRepository->updateAvailableQuantity($items["product_id"], ($productQty->available_quantity - 1));
        return $this->responseService->successResponse($cart->toArray(), SuccessMessages::cart_added);
    }

    public function removeProductToCart(array $items)
    {
        $findCart = $this->cartRepository->get($items["cart_id"]);
        if(!$findCart) {
            return $this->responseService->unavailable(ErrorMessages::cartItemDoesNotExists);
        }
        $productQty = $this->productRepository->get($findCart->product_id);
        $this->cartRepository->delete($findCart);
        $this->productRepository->updateAvailableQuantity($productQty->id, ($productQty->available_quantity + 1));
        return $this->responseService->noContentResponse();
    }

    public function list(int $user_id): object {
        $cart = $this->cartRepository->listByUserID($user_id);

        return $cart;
    }

}
