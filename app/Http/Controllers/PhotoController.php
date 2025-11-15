<?php

namespace App\Http\Controllers;

use App\Services\ExampleInterface;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function __construct(public ExampleInterface $service) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        echo "from photo controller <br>";
        return $this->service->implementMethod();
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
