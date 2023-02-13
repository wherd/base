<?php

namespace App\Routing;

#[\Attribute]
class Route
{
    public function __construct(public readonly string $method, public readonly string $uri)
    {
    }
}
