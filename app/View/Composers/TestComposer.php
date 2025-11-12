<?php

namespace App\View\Composers;

use Illuminate\View\View;

class TestComposer
{
    public function compose(View $view): void
    {
        $view->with(['datakey' => 'data-value']);
    }
}
