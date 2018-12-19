<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Slim\Views\Twig;
use Point\Models\User;
use Slim\Flash\Messages;
use Point\Models\AccessLevel;
use Point\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q'])) {
            $query = trim($request->getParam('q'));
            $users = User::where('username', 'like', '%' . $query . '%')->paginate(10)->appends(['q' => $query]);
        } else {
            $users = User::paginate(10);
        }
        return $twig->render($response, 'user/index.twig', compact('users'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        $levels = AccessLevel::get()->reverse();
        return $twig->render($response, 'user/create.twig', compact('levels'));
    }
    
    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (User::find($id)) {
            $user = User::find($id);
            $levels = AccessLevel::get()->reverse();
            return $twig->render($response, 'user/edit.twig', compact('user', 'levels'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    
    public function show(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (User::find($id)) {
            $user = User::find($id);
            return $twig->render($response, 'user/show.twig', compact('user'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash)
    {
        $validation = $validator->validate($request, [
            'username' => v::notEmpty()->unique('users', 'username'),
            'password' => v::notEmpty(),
            'password_confirmation' => v::sameValue(trim($request->getParam('password'))),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('users.create'));
        }

        User::create([
            'username' => Helpers::emptyToNull(($request->getParam('username'))),
            'password' => password_hash(Helpers::emptyToNull($request->getParam('password')), PASSWORD_DEFAULT),
            'access_level_id' => $request->getParam('access_level')
        ]);

        $flash->addMessage('success', 'Registrasi user berhasil!');

        return $response->withRedirect($router->pathFor('users.index'));
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, $id)
    {
        $validation = $validator->validate($request, [
            'username' => v::notEmpty()->uniqueExceptInitial('users', 'username', User::find($id)->username),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('users.edit', ['id' => $id]));
        }

        User::find($id)->update([
            'username' => Helpers::emptyToNull($request->getParam('username')),
            'access_level_id' => $request->getParam('access_level'),
        ]);

        $flash->addMessage('success', 'User berhasil diubah!');

        return $response->withRedirect($router->pathFor('users.show', ['id' => $id]));
    }
}