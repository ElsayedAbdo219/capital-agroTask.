<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\App\Interfaces\UserInterface;
use Modules\User\App\Interfaces\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
      $this->app->bind(UserInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
