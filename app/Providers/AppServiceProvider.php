<?php

namespace App\Providers;

use App\View\Composers\TestComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // to make constrait for id param
        Route::pattern('id', '[0-9]+');

        // ratelimition

        RateLimiter::for('example_limit', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());

            // return $request->user()?Limit::perMinute(3)->by($request->ip()):Limit::perMinute(1)->by($request->ip());-> limit guest more than user


        });

        // ----------------view composer------------

        // View::composer(['test.views.composertest'], TestComposer::class); #=>['*'] for all blade templates in first array arg

        // so like routes wwe can use closures!
        View::composer(['test.views.composertest'], function ($view) {
            $view->with('datakey', 'closuer');
        }); #=>['*'] for all blade templates in first array arg
    }
}
