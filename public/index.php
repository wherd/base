<?php

declare(strict_types=1);

use Wherd\Http\Request;
use Wherd\Http\Response;

define('ROOT', dirname(__DIR__));
define('WRITABLE', ROOT . '/writable');

/*
|-----------------------------------------------------
| Create The Application
|-----------------------------------------------------
|
| The first thing we will do is create a new
| application instance which serves as the "glue" for
| all the components, and is the IoC container for
| the system binding all of the various parts.
|
*/

$app = include ROOT . '/src/bootstrap.php';

/*
|-----------------------------------------------------
| Turn On The Lights
|-----------------------------------------------------
|
| This script returns the application instance. The
| instance is given to the calling script so we can
| separate the building of the instances from the
| actual running of the application and sending
| responses.
|
*/

$app->providerOf('kernel')->dispatch(new Request, new Response)->send();
