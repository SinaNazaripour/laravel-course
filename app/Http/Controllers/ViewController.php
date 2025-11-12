<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function basics()
    {
        return view('test.views.basics', ["name" => 'sina']);
    }

    public function workwithhtml()
    {
        return view('test.views.workwithhtml', ["name" => 'sina']);
    }

    public function composer()
    {
        return view('test.views.composertest');
    }

    public function ananymousComponents()
    {
        return view('test.views.include-ananymous-components');
    }

    public function classComponents()
    {
        return view('test.views.include-class-components');
    }

    public function componentBasedLayout()
    {
        return view('test.views.include-component-layout');
    }
}
