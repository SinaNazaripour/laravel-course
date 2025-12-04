<?php

use App\Exceptions\CustomException;
use App\Http\Middleware\AliasParamsMiddleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\SayHelloMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //adding global middleware
        // $middleware->append(SayHelloMiddleware::class);
        $middleware->alias([
            'aliastest.midlleware' => AliasParamsMiddleware::class
        ]);

        // real localization step 4

        $middleware->web(append: LocaleMiddleware::class); # middleware groups
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // // like in exception
        // $exceptions->report(function (CustomException $e) {
        //     Log::channel('myLog', $e->getMessage());
        // });

        // // and about render like report

        //  $exceptions->render(function (CustomException $e,Request $request) {
        //     return view...
        // });
    })->create();
