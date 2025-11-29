<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class CustomException extends Exception
{
    public function report()
    {
        // if not define this method error will bi logged auto in default log file also you can log sepecially

        Log::channel('myLog')->warning("cusromException error log");
    }

    public function render()
    {
        // it will be auto rendered when an exception is thrown

        return view('test.views.customException', ['error' => $this->getMessage()]);
    }
}
