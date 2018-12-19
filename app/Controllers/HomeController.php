<?php

namespace Point\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController
{
    public function home(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'home.twig');
    }
}