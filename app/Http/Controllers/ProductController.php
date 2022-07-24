<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Product\IProductService;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\SearchProductRequest;
use App\Services\Responses\IResponseService;
use App\Enums\SuccessMessages;
use App\Repositories\Product\IProductRepository;

class ProductController extends Controller
{
    private IProductService $productService;
    private IResponseService $responseService;
    private IProductRepository $productRepository;

    public function __construct(IProductService $productService,
                                IResponseService $responseService,
                                IProductRepository $productRepository)
    {
        $this->productService = $productService;
        $this->responseService = $responseService;
        $this->productRepository = $productRepository;
    }

    public function store(StoreProductRequest $request) 
    {
        $data = $request->validated();
        $product = $this->productService->store($data);
        return $this->responseService->successResponse($product->toArray(), SuccessMessages::product_added);
    }

    public function list(Request $request) 
    {
        $products = $this->productService->list($request->query("sortName"), $request->query("sortOrder"));
        return $this->responseService->successResponse($products->toArray(), SuccessMessages::success);
    }

    public function searchProduct(SearchProductRequest $request) 
    {
        $data = $request->validated();
        $products = $this->productRepository->searchProduct($request->name);
        return $this->responseService->successResponse($products->toArray(), SuccessMessages::success);
    }
}
