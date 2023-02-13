<?php

declare(strict_types=1);

namespace App\Routing;

use Wherd\Http\Response;
use Wherd\Http\Request;

abstract class Controller
{
    public function __construct(protected Request $request, protected Response $response)
    {
    }
}
