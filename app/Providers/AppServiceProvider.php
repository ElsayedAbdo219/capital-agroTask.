<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\App\Interfaces\UserInterface;
use Modules\User\App\Interfaces\UserRepository;
use Modules\Product\App\Interface\ProductInterface;
use Modules\Product\App\Interface\ProductRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
      $this->app->bind(UserInterface::class, UserRepository::class);
      $this->app->bind(ProductInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
