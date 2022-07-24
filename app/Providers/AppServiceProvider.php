<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\IAuthService;
use App\Services\Encryption\EncryptionService;
use App\Services\Encryption\IEncryptionService;
use Illuminate\Support\ServiceProvider;
use App\Services\Responses\IResponseService;
use App\Services\Responses\ResponseService;
use App\Services\Product\IProductService;
use App\Services\Product\ProductService;
use App\Services\Cart\ICartService;
use App\Services\Cart\CartService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IEncryptionService::class, EncryptionService::class);
        $this->app->bind(IAuthService::class, AuthService::class);
        $this->app->singleton(IResponseService::class, ResponseService::class);
        $this->app->bind(IProductService::class, ProductService::class);
        $this->app->bind(ICartService::class, CartService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
