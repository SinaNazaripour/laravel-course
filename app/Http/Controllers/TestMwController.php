<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ExampleMiddleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TestMwController extends Controller implements HasMiddleware
{


    /**
     * add middleware to  class or any method
     */
    public static function middleware()
    {
        // // return [new Middleware('auth')]; #for all metods
        // return [new Middleware('auth', only: ["create"])]; #for specefic method
        // return [new Middleware('auth',except:["index"])]; #fo except this

        return [
            function (Request $request, Closure $next) {
                echo "middleware from contrller";
                return $next($request);
            }
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return "testing internall middleware";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
