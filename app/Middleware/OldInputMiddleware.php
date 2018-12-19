<?php

namespace Point\Middleware;

use Slim\Router;
use Slim\Views\Twig;

class OldInputMiddleware
{
    protected $twig;

    protected $router;

    public function __construct(Twig $twig, Router $router)
    {
        $this->twig = $twig;
        $this->router = $router;
    }

    public function __invoke($request, $response, $next)
    {
        $path = $request->getUri()->getBasePath() . '/' . $request->getUri()->getPath();
        
        if ($path === $this->router->pathFor('storeReceipts.store')) {
            $response = $next($request, $response);
            return $response;
        }
        
        if ($path === $this->router->pathFor('cvReceipts.store')) {
            $response = $next($request, $response);
            return $response;
        }

        if ($path === $this->router->pathFor('profit.index')) {
            $response = $next($request, $response);
            return $response;
        }

        if (isset($_SESSION['old'])) {
            $this->twig->getEnvironment()->addGlobal('old', $_SESSION['old']);
        }

        $_SESSION['old'] = array_map('trim', $request->getParams());

        $response = $next($request, $response);
        return $response;
    }
}