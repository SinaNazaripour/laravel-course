<?php

namespace App\Services;

class ExampleService1
{
    public function __construct(protected ExampleService2 $service2)
    {
        echo "service one created<br>";
    }
    public function serviceOneMethod()
    {
        echo $this->service2->serviceTwoMethod();
        return 'Service 1 callled';
    }
}
