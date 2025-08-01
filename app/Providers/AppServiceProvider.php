<?php

namespace App\Providers;

use App\Domain\TravelOrder\Repositories\TravelOrderRepositoryInterface;
use App\Infrastructure\Repositories\TravelOrderRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TravelOrderRepositoryInterface::class, TravelOrderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
