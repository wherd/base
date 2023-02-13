<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Routing\Controller;
use Wherd\Foundation\System;
use Wherd\Http\Response;

class HomeController extends Controller
{
    #[\App\Routing\Route('GET', '/')]
    public function index(): Response
    {
        /** @var \Wherd\Signal\View */
        $view = System::getInstance()->providerOf('view');
        
        $this->response->setStatusCode(200);
        $this->response->html($view->render('/home/index'));

        return $this->response;
    }
}
