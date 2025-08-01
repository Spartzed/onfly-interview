<?php

namespace App\Providers;

use App\Domain\TravelOrder\Repositories\TravelOrderRepositoryInterface;
use App\Domain\Notification\Repositories\NotificationRepository;
use App\Infrastructure\Repositories\TravelOrderRepository;
use App\Infrastructure\Repositories\EloquentNotificationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TravelOrderRepositoryInterface::class, TravelOrderRepository::class);
        $this->app->bind(NotificationRepository::class, EloquentNotificationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
