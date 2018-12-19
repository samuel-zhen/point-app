<?php

use Slim\Router;
use Slim\Views\Twig;
use Slim\Csrf\Guard as Csrf;
use Point\Middleware\CsrfViewMiddleware;
use Point\Middleware\OldInputMiddleware;
use Point\Middleware\ValidationErrorsMiddleware;

$app->add(new CsrfViewMiddleware($container->get(Twig::class), $container->get(Csrf::class)));
$app->add(new ValidationErrorsMiddleware($container->get(Twig::class), $container->get(Router::class)));
$app->add(new OldInputMiddleware($container->get(Twig::class), $container->get(Router::class)));
$app->add($container->get(Csrf::class));