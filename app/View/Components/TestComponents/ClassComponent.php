<?php

namespace App\View\Components\TestComponents;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClassComponent extends Component
{
    /**
     * Create a new component instance.
     */


    public function __construct(public $fromClassComponent = 'data from class component')
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.test-components.class-component');
    }

    public function shouldRender()
    {
        return $this->fromClassComponent !== '';
    }
}
