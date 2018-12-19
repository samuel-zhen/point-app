<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Auth\Auth;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController
{
    public function getLogin(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'auth/login.twig');
    }

    public function postLogin(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, Auth $auth)
    {
        $authentication = $auth->attempt(
            $request->getParam('username'),
            $request->getParam('password')
        );

        if (!$authentication) {
            $flash->addMessage('error', 'Oops! Login gagal...');
            return $response->withRedirect($router->pathFor('auth.getLogin'));
        }

        return $response->withRedirect($router->pathFor('home'));
    }

    public function getChangePassword(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'auth/change-password.twig');
    }

    public function postChangePassword(ServerRequestInterface $request, ResponseInterface $response, Auth $auth, Validator $validator, Router $router, Messages $flash)
    {
        $validation = $validator->validate($request, [
            'old_password' => v::notEmpty()->matchesPassword($auth->user()->password),
            'new_password' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('auth.getChangePassword'));
        }

        $auth->user()->setPassword($request->getParam('new_password'));

        $flash->addMessage('success', 'Password anda sukses diganti!');

        return $response->withRedirect($router->pathFor('auth.getChangePassword'));
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response, Router $router, Auth $auth)
    {
        $auth->logout();

        return $response->withRedirect($router->pathFor('auth.getLogin'));
    }
}