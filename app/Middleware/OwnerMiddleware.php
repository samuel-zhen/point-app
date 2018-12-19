<?php

namespace Point\Middleware;

use Slim\Router;
use Point\Auth\Auth;

class OwnerMiddleware
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
        if ($this->auth->check() && $this->auth->user()->access_level_id === 1) {
            $response = $next($request, $response);
            return $response;
        }
        
        return $response->withRedirect($this->router->pathFor('home'));
    }
}