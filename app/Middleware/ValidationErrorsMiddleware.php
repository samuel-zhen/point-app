<?php

namespace Point\Middleware;

use Slim\Router;
use Slim\Views\Twig;

class ValidationErrorsMiddleware
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

        if (isset($_SESSION['validation_errors'])) {
            $this->twig->getEnvironment()->addGlobal('errors', $_SESSION['validation_errors']);
            unset($_SESSION['validation_errors']);
        }

        $response = $next($request, $response);
        return $response;
    }
}