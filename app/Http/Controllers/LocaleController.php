<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function locale(string $locale = 'en')
    {
        if (($locale !== 'en')) {
            // config(['app.locale' => $locale]);
            App::setlocale($locale);
        }

        return view('test.views.localization');
    }
}
