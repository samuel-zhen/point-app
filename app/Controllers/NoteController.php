<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Point\Auth\Auth;
use Slim\Views\Twig;
use Point\Models\Note;
use Slim\Flash\Messages;
use Point\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NoteController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        $notes = Note::orderBy('created_at', 'desc')->paginate(15);
        return $twig->render($response, 'note/index.twig', compact('notes'));
    }
    
    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'note/create.twig');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (Note::find($id)) {
            $note = Note::find($id);

            return $twig->render($response, 'note/edit.twig', compact('note'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, Auth $auth)
    {
        $validation = $validator->validate($request, [
            'catatan' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('notes.create'));
        }

        Note::create([
            'body' => Helpers::emptyToNull($request->getParam('catatan')),
            'user_id' => $auth->user()->id,
        ]);

        $flash->addMessage('success', 'Catatan berhasil disimpan!');

        return $response->withRedirect($router->pathFor('notes.index'));
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, Auth $auth, $id)
    {
        $validation = $validator->validate($request, [
            'catatan' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('notes.edit', ['id' => $id]));
        }

        Note::find($id)->update([
            'body' => Helpers::emptyToNull($request->getParam('catatan')),
            'user_id' => $auth->user()->id,
        ]);

        $flash->addMessage('success', 'Catatan berhasil diubah!');

        return $response->withRedirect($router->pathFor('notes.index'));
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (Note::find($id)) {
            Note::destroy($id);
            $flash->addMessage('success', 'Catatan berhasil dihapus!');
    
            return $response->withRedirect($router->pathFor('notes.index'));
        }
    }
}