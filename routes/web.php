<?php

use Slim\Router;
use Point\Auth\Auth;
use Slim\Flash\Messages;
use Point\Models\ProductCategory;
use Point\Middleware\AuthMiddleware;
use Point\Controllers\AuthController;
use Point\Controllers\DebtController;
use Point\Controllers\HomeController;
use Point\Controllers\NoteController;
use Point\Controllers\UserController;
use Point\Middleware\GuestMiddleware;
use Point\Middleware\OwnerMiddleware;
use Point\Controllers\ProfitController;
use Point\Controllers\CustomerController;
use Point\Controllers\CVReceiptController;
use Point\Controllers\SellPriceController;
use Point\Controllers\WorkOrderController;
use Point\Controllers\StoreReceiptController;
use Point\Middleware\NotAuthorizedMiddleware;
use Point\Controllers\PurchasePriceController;
use Point\Controllers\ProductCategoryController;

$app->group('', function() {
    $this->get('/', [AuthController::class, 'getLogin'])->setName('auth.getLogin');
    $this->post('/', [AuthController::class, 'postLogin'])->setName('auth.postLogin');
})->add(new GuestMiddleware(
    $container->get(Auth::class),
    $container->get(Router::class)
));

$app->group('', function() use ($container) {
    $this->get('/change-password', [AuthController::class, 'getChangePassword'])->setName('auth.getChangePassword');
    $this->post('/change-password', [AuthController::class, 'postChangePassword'])->setName('auth.postChangePassword');
    $this->get('/logout', [AuthController::class, 'logout'])->setName('auth.logout');
    
    $this->get('/home', [HomeController::class, 'home'])->setName('home');

    $this->group('/work-orders', function() use ($container) {
        $this->get('/ongoing', [WorkOrderController::class, 'ongoing'])->setName('workOrders.ongoing');
        $this->get('/completed', [WorkOrderController::class, 'completed'])->setName('workOrders.completed');
        $this->get('/create', [WorkOrderController::class, 'create'])->setName('workOrders.create');
        $this->get('/{id}', [WorkOrderController::class, 'show'])->setName('workOrders.show');
        $this->get('/{id}/edit', [WorkOrderController::class, 'edit'])->setName('workOrders.edit');
        $this->get('/{id}/print', [WorkOrderController::class, 'print'])->setName('workOrders.print');
        $this->delete('/delete/{id}', [WorkOrderController::class, 'delete'])->setName('workOrders.delete')->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));
        $this->post('', [WorkOrderController::class, 'store'])->setName('workOrders.store');
        $this->patch('/complete/{id}', [WorkOrderController::class, 'isCompleted'])->setName('workOrders.isCompleted');
        $this->patch('/ongoing/{id}', [WorkOrderController::class, 'isOngoing'])->setName('workOrders.isOngoing');
        $this->map(['PUT', 'PATCH'], '/{id}', [WorkOrderController::class, 'update'])->setName('workOrders.update');
    });

    $this->group('/customers', function() {
        $this->get('', [CustomerController::class, 'index'])->setName('customers.index');
        $this->get('/create', [CustomerController::class, 'create'])->setName('customers.create');
        $this->get('/{id}', [CustomerController::class, 'show'])->setName('customers.show');
        $this->get('/{id}/edit', [CustomerController::class, 'edit'])->setName('customers.edit');
        $this->post('', [CustomerController::class, 'store'])->setName('customers.store');
        $this->delete('/{id}', [CustomerController::class, 'delete'])->setName('customers.delete');
        $this->map(['PUT', 'PATCH'], '/{id}', [CustomerController::class, 'update'])->setName('customers.update');
    });

    $this->group('/users', function() {
        $this->get('', [UserController::class, 'index'])->setName('users.index');
        $this->get('/create', [UserController::class, 'create'])->setName('users.create');
        $this->get('/{id}', [UserController::class, 'show'])->setName('users.show');
        $this->get('/{id}/edit', [UserController::class, 'edit'])->setName('users.edit');
        $this->post('', [UserController::class, 'store'])->setName('users.store');
        $this->map(['PUT', 'PATCH'], '/{id}', [UserController::class, 'update'])->setName('users.update');
    })->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));

    $this->group('/notes', function() {
        $this->get('', [NoteController::class, 'index'])->setName('notes.index');
        $this->get('/create', [NoteController::class, 'create'])->setName('notes.create');
        $this->get('/{id}/edit', [NoteController::class, 'edit'])->setName('notes.edit');
        $this->post('', [NoteController::class, 'store'])->setName('notes.store');
        $this->delete('/{id}', [NoteController::class, 'delete'])->setName('notes.delete');
        $this->map(['PUT', 'PATCH'], '/{id}', [NoteController::class, 'update'])->setName('notes.update');
    });

    $this->group('/price-list', function() use ($container) {
        $this->get('/sell', [SellPriceController::class, 'index'])->setName('sellPrices.index');
        $this->get('/sell/create', [SellPriceController::class, 'create'])->setName('sellPrices.create');
        $this->post('/sell', [SellPriceController::class, 'store'])->setName('sellPrices.store');
        
        $this->group('', function() {
            $this->get('/sell/{id}/edit', [SellPriceController::class, 'edit'])->setName('sellPrices.edit');
            $this->delete('/sell/{id}', [SellPriceController::class, 'delete'])->setName('sellPrices.delete');
            $this->map(['PUT', 'PATCH'], '/sell/{id}', [SellPriceController::class, 'update'])->setName('sellPrices.update');
        })->add(new NotAuthorizedMiddleware($container->get(Auth::class), $container->get(Router::class)));
    
        $this->group('', function() {
            $this->get('/purchase', [PurchasePriceController::class, 'index'])->setName('purchasePrices.index');
            $this->get('/purchase/create', [PurchasePriceController::class, 'create'])->setName('purchasePrices.create');
            $this->get('/purchase/{id}/edit', [PurchasePriceController::class, 'edit'])->setName('purchasePrices.edit');
            $this->post('/purchase', [PurchasePriceController::class, 'store'])->setName('purchasePrices.store');
            $this->delete('/purchase/{id}', [PurchasePriceController::class, 'delete'])->setName('purchasePrices.delete');
            $this->map(['PUT', 'PATCH'], '/purchase/{id}', [PurchasePriceController::class, 'update'])->setName('purchasePrices.update');
            
            $this->get('/category', [ProductCategoryController::class, 'index'])->setName('categories.index');
            $this->get('/category/create', [ProductCategoryController::class, 'create'])->setName('categories.create');
            $this->get('/category/{id}/edit', [ProductCategoryController::class, 'edit'])->setName('categories.edit');
            $this->post('/category', [ProductCategoryController::class, 'store'])->setName('categories.store');
            $this->delete('/category/{id}', [ProductCategoryController::class, 'delete'])->setName('categories.delete');
            $this->map(['PUT', 'PATCH'], '/category/{id}', [ProductCategoryController::class, 'update'])->setName('categories.update');
        })->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));
    });

    $this->group('/receipts/store', function() use ($container) {
        $this->get('/create', [StoreReceiptController::class, 'create'])->setName('storeReceipts.create');
        $this->get('/settled', [StoreReceiptController::class, 'settledList'])->setName('storeReceipts.settledList');
        $this->get('/debt', [StoreReceiptController::class, 'debtList'])->setName('storeReceipts.debtList');
        $this->get('/canceled', [StoreReceiptController::class, 'canceledList'])->setName('storeReceipts.canceledList')->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));
        $this->get('/{id}', [StoreReceiptController::class, 'show'])->setName('storeReceipts.show');
        $this->get('/{id}/print', [StoreReceiptController::class, 'print'])->setName('storeReceipts.print');
        $this->post('', [StoreReceiptController::class, 'store'])->setName('storeReceipts.store');
        $this->patch('/settled/{id}', [StoreReceiptController::class, 'settled'])->setName('storeReceipts.settled');
        $this->patch('/debt/{id}', [StoreReceiptController::class, 'debt'])->setName('storeReceipts.debt');
        $this->delete('/{id}', [StoreReceiptController::class, 'cancel'])->setName('storeReceipts.cancel')->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));
    });
    
    $this->group('/receipts/cv', function() use ($container) {
        $this->get('/create', [CVReceiptController::class, 'create'])->setName('cvReceipts.create');
        $this->get('/settled', [CVReceiptController::class, 'settledList'])->setName('cvReceipts.settledList');
        $this->get('/debt', [CVReceiptController::class, 'debtList'])->setName('cvReceipts.debtList');
        $this->get('/canceled', [CVReceiptController::class, 'canceledList'])->setName('cvReceipts.canceledList')->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));
        $this->get('/{id}', [CVReceiptController::class, 'show'])->setName('cvReceipts.show');
        $this->get('/{id}/print', [CVReceiptController::class, 'print'])->setName('cvReceipts.print');
        $this->post('', [CVReceiptController::class, 'store'])->setName('cvReceipts.store');
        $this->patch('/settled/{id}', [CVReceiptController::class, 'settled'])->setName('cvReceipts.settled');
        $this->patch('/debt/{id}', [CVReceiptController::class, 'debt'])->setName('cvReceipts.debt');
        $this->delete('/{id}', [CVReceiptController::class, 'cancel'])->setName('cvReceipts.cancel')->add(new OwnerMiddleware($container->get(Auth::class), $container->get(Router::class)));
    });
    
    $this->get('/debt/store', [DebtController::class, 'fromStore'])->setName('debt.fromStore');
    $this->get('/debt/cv', [DebtController::class, 'fromCV'])->setName('debt.fromCV');
    $this->get('/profit', [ProfitController::class, 'index'])->setName('profit.index');

})->add(new AuthMiddleware(
    $container->get(Auth::class), 
    $container->get(Messages::class), 
    $container->get(Router::class)
));

$app->get('/api/customers', [CustomerController::class, 'names']);