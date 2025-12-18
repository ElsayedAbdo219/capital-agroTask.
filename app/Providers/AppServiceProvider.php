<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\App\Interface\OrderInterface;
use Modules\App\Repository\OrderRepository;
use Modules\User\App\Interfaces\UserInterface;
use Modules\User\App\Interfaces\UserRepository;
use Modules\Product\App\Interface\ProductInterface;
use Modules\Product\App\Interface\ProductRepository;
use Modules\ReturnProduct\App\Interface\ReturnProductInterface;
use Modules\ReturnProduct\App\Repository\ReturnProductRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
      $this->app->bind(UserInterface::class, UserRepository::class);
      $this->app->bind(ProductInterface::class, ProductRepository::class);
      $this->app->bind(OrderInterface::class, OrderRepository::class);
      $this->app->bind(ReturnProductInterface::class, ReturnProductRepository::class);
      
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
