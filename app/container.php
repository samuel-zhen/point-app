<?php

use Slim\Router;
use Point\Auth\Auth;
use Slim\Views\Twig;
use Point\View\Factory;
use Slim\Flash\Messages;
use Slim\Csrf\Guard as Csrf;
use Point\Validation\Validator;
use Point\Errors\NotFoundHandler;
use Psr\Container\ContainerInterface;
use Illuminate\Database\Capsule\Manager as Capsule;

return [
    Router::class => function (ContainerInterface $c) {
        return $c->get('router');
    },
    Twig::class => function (ContainerInterface $c) {
        $twig = Factory::getEngine();
        
        $basePath = rtrim(str_ireplace('index.php', '', $c->get('request')->getUri()->getBasePath()), '/');
        $twig->addExtension(new Slim\Views\TwigExtension($c->get('router'), $basePath));
        $twig->addExtension(new Twig_Extension_Debug());
        
        $twig->getEnvironment()->getExtension('Twig_Extension_Core')->setNumberFormat(0, ',', '.');
        $twig->getEnvironment()->getExtension('Twig_Extension_Core')->setDateFormat('d/m/Y');

        $twig->getEnvironment()->addGlobal('GET', $_GET);
        $twig->getEnvironment()->addGlobal('flash', $c->get(Messages::class));
        $twig->getEnvironment()->addGlobal('auth', [
            'check' => $c->get(Auth::class)->check(),
            'user' => $c->get(Auth::class)->user(),
        ]);
        
        return $twig;
    },
    Csrf::class => function (ContainerInterface $c) {
        return new Csrf;
    },
    Validator::class => function (ContainerInterface $c) {
        return new Validator;
    },
    Messages::class => function (ContainerInterface $c) {
        return new Messages;
    },
    Auth::class => function (ContainerInterface $c) {
        return new Auth;
    },
    'notFoundHandler' => function (ContainerInterface $c) {
        return new NotFoundHandler($c->get(Twig::class), '404.twig');
    }
];