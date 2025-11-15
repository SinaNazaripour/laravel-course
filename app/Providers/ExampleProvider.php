<?php

namespace App\Providers;

use App\Http\Controllers\PhotoController;
use App\Services\{EmailNotificationService, ExampleImplementedService, ExampleImplementedService2, ExampleInterface, ExampleService1, ExampleService2, NotificationDispatcher, SMSNotificationService};
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ExampleProvider extends ServiceProvider #implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // |----------------------
        // |---- binding unresolved dependencies
        // |---------------------
        // $this->app->bind(ExampleService1::class, function () {
        //     return new ExampleService1($this->app->make(ExampleService2::class));
        // });
        $this->app->singleton(ExampleService1::class, function () {
            return new ExampleService1($this->app->make(ExampleService2::class));
        });
        // |----------------------
        // |---- interface binding
        // |---------------------
        $this->app->bind(ExampleInterface::class, ExampleImplementedService::class);

        // |----------------------
        // |---- binding interfaces at runtime 
        // |---------------------
        $this->app->when([PhotoController::class])->needs(ExampleInterface::class)->give(function () {
            return new ExampleImplementedService2;
        });

        // |----------------------
        // |---- tagged bindings
        // |---------------------
        $this->app->tag([SMSNotificationService::class, EmailNotificationService::class], 'ntfcs');

        $this->app->bind(NotificationDispatcher::class, function () {
            return new NotificationDispatcher(...$this->app->tagged('ntfcs'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
