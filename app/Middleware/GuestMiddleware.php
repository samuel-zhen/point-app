<?php

namespace Point\Middleware;

use Slim\Router;
use Point\Auth\Auth;

class GuestMiddleware
{
    protected $auth;

    protected $router;

    public function __construct(Auth $auth, Router $router)
    {
        $this->auth = $auth;
        $this->router = $router;
    }

    public function __invoke($request, $response, $next)
    {
        if ($this->auth->check()) {
            return $response->withRedirect($this->router->pathFor('home'));
        }

        $response = $next($request, $response);
        return $response;
    }
}