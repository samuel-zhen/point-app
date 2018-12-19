<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Models\Customer;
use Point\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomerController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q'])) {
            $query = trim($request->getParam('q'));
            $customers = Customer::where('name', 'like', '%'. $query . '%')->paginate(20)->appends(['q' => $query]);
        } else {
            $customers = Customer::paginate(20);
        }

        return $twig->render($response, 'customer/index.twig', compact('customers'));
    }
    
    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'customer/create.twig');
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash)
    {
        $validation = $validator->validate($request, [
            'name' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('customers.create'));
        }

        Customer::create([
            'name' => Helpers::emptyToNull($request->getParam('name')),
            'phone_number' => Helpers::emptyToNull($request->getParam('phone_number')),
            'company' => Helpers::emptyToNull($request->getParam('company')),
            'address' => Helpers::emptyToNull($request->getParam('address')),
        ]);

        $flash->addMessage('success', 'Data pelanggan berhasil disimpan!');

        return $response->withRedirect($router->pathFor('customers.index'));
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (Customer::find($id)) {
            $customer = Customer::find($id);

            return $twig->render($response, 'customer/show.twig', compact('customer'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (Customer::find($id)) {
            $customer = Customer::find($id);

            return $twig->render($response, 'customer/edit.twig', compact('customer'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, $id)
    {
        $validation = $validator->validate($request, [
            'name' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('customers.edit', ['id' => $id]));
        }

        Customer::find($id)->update([
            'name' => Helpers::emptyToNull($request->getParam('name')),
            'phone_number' => Helpers::emptyToNull($request->getParam('phone_number')),
            'company' => Helpers::emptyToNull($request->getParam('company')),
            'address' => Helpers::emptyToNull($request->getParam('address')),
        ]);

        $flash->addMessage('success', 'Data pelanggan berhasil diubah!');

        return $response->withRedirect($router->pathFor('customers.show', ['id' => $id]));
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (Customer::find($id)) {
            Customer::destroy($id);
            
            $flash->addMessage('success', 'Data pelanggan berhasil dihapus!');
    
            return $response->withRedirect($router->pathFor('customers.index'));
        }
    }

    public function names(ServerRequestInterface $request, ResponseInterface $response)
    {
        $query = $request->getParam('q');
        $names = Customer::where('name', 'like', $query . '%')->orderBy('name', 'asc')->pluck('name');
        $apiNames['names'] = $names->map(function ($name) {
            return ['title' => $name];
        });

        return $response->withJson($names);
    }
}