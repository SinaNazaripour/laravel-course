<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function basics()
    {
        return view('test.views.basics', ["name" => 'sina']);
    }
}
