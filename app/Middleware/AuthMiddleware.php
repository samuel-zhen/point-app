<?php

namespace Point\Middleware;

use Slim\Router;
use Point\Auth\Auth;
use Slim\Flash\Messages;

class AuthMiddleware
{
    protected $auth;

    protected $flash;

    protected $router;

    public function __construct(Auth $auth, Messages $flash, Router $router)
    {
        $this->auth = $auth;
        $this->flash = $flash;
        $this->router = $router;
    }

    public function __invoke($request, $response, $next)
    {
        if (!$this->auth->check()) {
            $this->flash->addMessage('error', 'Silahkan login terlebih dahulu.');

            return $response->withRedirect($this->router->pathFor('auth.getLogin'));
        }

        $response = $next($request, $response);
        return $response;
    }
}