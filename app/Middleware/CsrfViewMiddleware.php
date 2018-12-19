<?php

namespace Point\Middleware;

use Slim\Views\Twig;
use Slim\Csrf\Guard as Csrf;

class CsrfViewMiddleware
{
    protected $twig;

    protected $csrf;

    public function __construct(Twig $twig, Csrf $csrf)
    {
        $this->twig = $twig;
        $this->csrf = $csrf;
    }

    public function __invoke($request, $response, $next)
    {
        $this->twig->getEnvironment()->addGlobal('csrf', [
            'fields' => '
                <input type="hidden" name="' . $this->csrf->getTokenNameKey() . '" value="' . $this->csrf->getTokenName() . '">
                <input type="hidden" name="' . $this->csrf->getTokenValueKey() . '" value="' . $this->csrf->getTokenValue() . '">
            ',
        ]);

        $response = $next($request, $response);
        return $response;
    }
}