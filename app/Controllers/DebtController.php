<?php

namespace Point\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Point\Models\Receipt\CV\Receipt as CVReceipt;
use Point\Models\Receipt\Store\Receipt as StoreReceipt;

class DebtController
{
    public function fromStore(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (!isset($_GET['customer'])) {
            return $twig->render($response, 'calculations/debt/fromStore.twig');
        }

        $customer = trim($request->getParam('customer'));
        $storeReceipts = StoreReceipt::where('customer', $customer)
                    ->where('is_settled', 0)
                    ->where('is_deleted', 0)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->appends($request->getParams());
        $storeSum = StoreReceipt::where('customer', $customer)
                    ->where('is_settled', 0)
                    ->where('is_deleted', 0)
                    ->sum('total');

        return $twig->render($response, 'calculations/debt/fromStore.twig', compact('storeReceipts', 'storeSum'));
    }

    public function fromCV(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (!isset($_GET['customer'])) {
            return $twig->render($response, 'calculations/debt/fromCV.twig');
        }

        $customer = trim($request->getParam('customer'));
        $cvReceipts = CVReceipt::where('customer', $customer)
                    ->where('is_settled', 0)
                    ->where('is_deleted', 0)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20)
                    ->appends($request->getParams());
        $cvSum = CVReceipt::where('customer', $customer)
                ->where('is_settled', 0)
                ->where('is_deleted', 0)
                ->sum('total');

        return $twig->render($response, 'calculations/debt/fromCV.twig', compact('cvReceipts', 'cvSum'));
    }
}