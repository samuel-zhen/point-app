<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Validation\Validator;
use Point\Models\ProductCategory;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProductCategoryController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q'])) {
            $query = trim($request->getParam('q'));
            $productCategories = ProductCategory::where('name', 'like', '%' . $query . '%')->orderBy('name')->paginate(20)->appends(['q' => $query]);
        } else {
            $productCategories = ProductCategory::orderBy('name')->paginate(20);
        }

        return $twig->render($response, 'price-list/category/index.twig', compact('productCategories'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'price-list/category/create.twig');
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (ProductCategory::find($id)) {
            $category = ProductCategory::find($id);
        
            return $twig->render($response, 'price-list/category/edit.twig', compact('category'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash)
    {
        $validation = $validator->validate($request, [
            'category_name' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('categories.create'));
        }

        ProductCategory::create([
            'name' => Helpers::emptyToNull($request->getParam('category_name')),
        ]);

        $flash->addMessage('success', 'Kategori barang berhasil disimpan!');

        return $response->withRedirect($router->pathFor('categories.index'));
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, $id)
    {
        $validation = $validator->validate($request, [
            'category_name' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('categories.edit', ['id' => $id]));
        }

        ProductCategory::where('id', $id)->update([
            'name' => Helpers::emptyToNull($request->getParam('category_name')),
        ]);

        $flash->addMessage('success', 'Kategori barang berhasil diubah!');

        return $response->withRedirect($router->pathFor('categories.index'));
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (ProductCategory::find($id)) {
            $category = ProductCategory::find($id);
            $purchasePrices = $category->purchasePrices()->get();
            
            foreach ($purchasePrices as $purchasePrice) {
                $purchasePrice->category_id = 1;
                $purchasePrice->save();
            }

            $category->delete();

            $flash->addMessage('success', 'Kategori barang berhasil dihapus!');
    
            return $response->withRedirect($router->pathFor('categories.index'));
        }
    }
}