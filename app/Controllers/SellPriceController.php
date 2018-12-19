<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Models\SellPrice;
use Point\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SellPriceController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q'])) {
            $query = trim($request->getParam('q'));
            $sellPrices = SellPrice::where('product_name', 'like', '%' . $query . '%')->orderBy('product_name')->paginate(20)->appends(['q' => $query]);
        } else {
            $sellPrices = SellPrice::orderBy('product_name')->paginate(20);
        }

        return $twig->render($response, 'price-list/sell/index.twig', compact('sellPrices'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'price-list/sell/create.twig');
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash)
    {
        $validation = $validator->validate($request, [
            'product_name' => v::notEmpty(),
            'price' => v::numeric(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('sellPrices.create'));
        }

        SellPrice::create([
            'product_name' => Helpers::emptyToNull($request->getParam('product_name')),
            'price' => Helpers::thousandFormat($request->getParam('price')),
        ]);

        $flash->addMessage('success', 'Harga jual berhasil disimpan!');

        return $response->withRedirect($router->pathFor('sellPrices.index'));
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (SellPrice::find($id)) {
            $sellPrice = SellPrice::find($id);
            return $twig->render($response, 'price-list/sell/edit.twig', compact('sellPrice'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, $id)
    {
        $validation = $validator->validate($request, [
            'product_name' => v::notEmpty(),
            'price' => v::numeric(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('sellPrices.edit', ['id' => $id]));
        }

        SelLPrice::find($id)->update([
            'product_name' => Helpers::emptyToNull($request->getParam('product_name')),
            'price' => Helpers::thousandFormat($request->getParam('price')),
        ]);

        $flash->addMessage('success', 'Harga jual berhasil diubah!');

        return $response->withRedirect($router->pathFor('sellPrices.index'));
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (SellPrice::find($id)) {
            SellPrice::destroy($id);

            $flash->addMessage('success', 'Harga jual berhasil dihapus!');
    
            return $response->withRedirect($router->pathFor('sellPrices.index'));
        }
    }
}