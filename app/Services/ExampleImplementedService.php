<?php

namespace App\Services;

class ExampleImplementedService implements ExampleInterface
{
    public function implementMethod()
    {
        return 'currect concrete class is executed';
    }
}
