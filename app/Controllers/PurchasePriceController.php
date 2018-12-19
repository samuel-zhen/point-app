<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Models\PurchasePrice;
use Point\Validation\Validator;
use Point\Models\ProductCategory;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PurchasePriceController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q'])) {
            $query = trim($request->getParam('q'));
            $purchasePrices = PurchasePrice::where('product_name', 'like', '%' . $query . '%')->orderBy('category_id')->paginate(20)->appends(['q' => $query]);
        } else {
            $purchasePrices = PurchasePrice::orderBy('category_id')->paginate(20);
        }

        return $twig->render($response, 'price-list/purchase/index.twig', compact('purchasePrices'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        $productCategories = ProductCategory::orderBy('name')->get();
        return $twig->render($response, 'price-list/purchase/create.twig', compact('productCategories'));
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash)
    {
        $validation = $validator->validate($request, [
            'product_name' => v::notEmpty(),
            'category_id' => v::notEmpty(),
            'price' => v::numeric(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('purchasePrices.create'));
        }

        PurchasePrice::create([
            'product_name' => Helpers::emptyToNull($request->getParam('product_name')),
            'category_id' => Helpers::emptyToNull($request->getParam('category_id')),
            'price' => Helpers::thousandFormat($request->getParam('price')),
        ]);

        $flash->addMessage('success', 'Harga pembelian berhasil disimpan!');

        return $response->withRedirect($router->pathFor('purchasePrices.index'));
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (PurchasePrice::find($id)) {
            $purchasePrice = PurchasePrice::find($id);
            $productCategories = ProductCategory::orderBy('name')->get();
            return $twig->render($response, 'price-list/purchase/edit.twig', compact('purchasePrice', 'productCategories'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, $id)
    {
        $validation = $validator->validate($request, [
            'product_name' => v::notEmpty(),
            'category_id' => v::notEmpty(),
            'price' => v::numeric(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('purchasePrices.edit', ['id' => $id]));
        }

        PurchasePrice::where('id', $id)->update([
            'product_name' => Helpers::emptyToNull($request->getParam('product_name')),
            'category_id' => Helpers::emptyToNull($request->getParam('category_id')),
            'price' => Helpers::thousandFormat($request->getParam('price')),
        ]);

        $flash->addMessage('success', 'Harga pembelian berhasil diubah!');

        return $response->withRedirect($router->pathFor('purchasePrices.index'));
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (PurchasePrice::find($id)) {
            PurchasePrice::destroy($id);
    
            $flash->addMessage('success', 'Harga pembelian berhasil dihapus!');
    
            return $response->withRedirect($router->pathFor('purchasePrices.index'));
        }
    }
}