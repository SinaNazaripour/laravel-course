<?php

use App\Http\Middleware\AliasParamsMiddleware;
use App\Http\Middleware\SayHelloMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
