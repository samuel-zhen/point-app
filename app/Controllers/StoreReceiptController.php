<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Point\Auth\Auth;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Models\WorkOrder;
use Point\Models\Receipt\Store\Receipt;
use Psr\Http\Message\ResponseInterface;
use Point\Models\Receipt\Store\ItemsReceipt;
use Psr\Http\Message\ServerRequestInterface;
use Point\Models\Receipt\Store\OrdersReceipt;
use Point\Interfaces\ReceiptControllerInterface;

class StoreReceiptController
{
    protected function insertOrders($receiptId, $workOrders)
    {
        foreach ($workOrders as $id => $workOrder) {
            OrdersReceipt::create([
                'receipt_id'    => $receiptId,
                'work_order_id' => $id,
                'quantity'      => Helpers::thousandFormat($workOrder['quantity']),
                'unit'          => trim($workOrder['unit']),
                'price'         => Helpers::thousandFormat($workOrder['price']),
                'product'       => trim($workOrder['product']),
                'total'         => Helpers::thousandFormat($workOrder['subtotal']),
            ]);

            WorkOrder::find($id)->update([
                'is_in_receipt' => 1,
                'is_complete' => 1,
            ]);
        }
    }

    protected function insertItems($receiptId, $items)
    {
        foreach ($items as $item) {
            ItemsReceipt::create([
                'receipt_id'    => $receiptId,
                'quantity'      => Helpers::thousandFormat($item['quantity']),
                'unit'          => trim($item['unit']),
                'product'       => trim($item['product']),
                'price'         => Helpers::thousandFormat($item['price']),
                'total'         => Helpers::thousandFormat($item['subtotal']),
            ]);
        }
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Auth $auth, Router $router, Messages $flash)
    {
        $receipt = Receipt::create([
            'user_id'   => $auth->user()->id,
            'customer'  => trim($request->getParam('customer')),
            'total'     => trim($request->getParam('total')),
        ]);
        $receiptId = $receipt->id;
        $workOrders = $request->getParam('workOrder');
        $items = $request->getParam('manual');
        
        if ($workOrders !== null) {
            $this->insertOrders($receiptId, $workOrders);
        }

        if ($items !== null) {
            $this->insertItems($receiptId, $items);
        }

        $flash->addMessage('success', 'Nota berhasil disimpan!');

        return $response->withRedirect($router->pathFor('storeReceipts.show', ['id' => $receiptId]));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['customer'])) {
            $customerName = trim($request->getParam('customer'));
            $workOrders = WorkOrder::where('customer', $customerName)->where('is_in_receipt', 0)->get();
            return $twig->render($response, 'receipt/store/create.twig', compact('customerName', 'workOrders')); 
        } else {
            $customerName = null;
            return $twig->render($response, 'receipt/store/create.twig', compact('customerName'));
        }
    }
    
    public function settledList(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q']) && isset($_GET['col'])) {
            $query = trim($_GET['q']);
            $column = trim($_GET['col']);
            $receipts = Receipt::where('is_settled', 1)
                    ->where('is_deleted', 0)
                    ->where($column, $query)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->appends(['col' => $column, 'q' => $query]);
        } else {
            $receipts = Receipt::where('is_settled', 1)
                        ->where('is_deleted', 0)
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
        }

        return $twig->render($response, 'receipt/store/settled.twig', compact('receipts'));
    }
    
    public function canceledList(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q']) && isset($_GET['col'])) {
            $query = trim($_GET['q']);
            $column = trim($_GET['col']);
            $receipts = Receipt::where('is_deleted', 1)
                    ->where($column, $query)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->appends(['q' => $query, 'col' => $column]);
        } else {
            $receipts = Receipt::where('is_deleted', 1)
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
        }
        return $twig->render($response, 'receipt/store/canceled.twig', compact('receipts'));
    }
    
    public function debtList(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q']) && isset($_GET['col'])) {
            $query = trim($_GET['q']);
            $column = trim($_GET['col']);
            $receipts = Receipt::where('is_settled', 0)
                    ->where('is_deleted', 0)
                    ->where($column, $query)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->appends(['q' => $query, 'col' => $column]);
        } else {
            $receipts = Receipt::where('is_settled', 0)
                        ->where('is_deleted', 0)
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
        }
        return $twig->render($response, 'receipt/store/debt.twig', compact('receipts'));
    }
    
    public function show(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (Receipt::find($id)) {
            $receipt = Receipt::find($id);
            $workOrders = $receipt->workOrders()->get();
            $items = $receipt->items()->get();
            
            return $twig->render($response, 'receipt/store/show.twig', compact('receipt', 'workOrders', 'items'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }
    
    public function settled(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (Receipt::find($id)) {
            Receipt::find($id)->update([
                'is_settled' => 1,
                'settled_date' => date('Y-m-d H:i:s'),
            ]);
            
            $flash->addMessage('success', 'Status nota berhasil diubah!');

            return $response->withRedirect($router->pathFor('storeReceipts.show', ['id' => $id]));
        }
    }
    
    public function debt(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (Receipt::find($id)) {
            Receipt::find($id)->update([
                'is_settled' => 0,
                'settled_date' => null,
            ]);
            
            $flash->addMessage('success', 'Status nota berhasil diubah!');

            return $response->withRedirect($router->pathFor('storeReceipts.show', ['id' => $id]));
        }
    }

    public function cancel(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (Receipt::find($id)) {
            $receipt = Receipt::find($id);
            $receipt->update([
                'is_deleted' => 1,
            ]);
            
            $workOrders = $receipt->works()->get();
                
            if ($workOrders !== null) {
                foreach ($workOrders as $workOrder) {
                    $workOrder->update([
                        'is_in_receipt' => 0,
                    ]);
                }
            }

            $flash->addMessage('success', 'Nota berhasil dibatalkan!');

            return $response->withRedirect($router->pathFor('storeReceipts.show', ['id' => $id]));
        }
    }

    public function print(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (Receipt::find($id)) {
            $receipt = Receipt::find($id);
            $workOrders = $receipt->workOrders()->get();
            $items = $receipt->items()->get();
            
            return $twig->render($response, 'receipt/store/print.twig', compact('receipt', 'workOrders', 'items'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }
}