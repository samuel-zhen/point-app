<?php

namespace Point\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Models\Receipt\Store\Receipt;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ProfitController
{
    public function index(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, Messages $flash, Router $router)
    {
        if (!isset($_GET['start_date']) && !isset($_GET['end_date'])) {
            return $twig->render($response, 'calculations/profit/index.twig');
        }

        $startDateArr = $_GET['start_date'];
        $endDateArr = $_GET['end_date'];

        $startDate = (new \DateTime("{$startDateArr['year']}-{$startDateArr['month']}-{$startDateArr['day']} 00:00:00"))->format('Y-m-d H:i:s');
        $endDate = (new \DateTime("{$endDateArr['year']}-{$endDateArr['month']}-{$endDateArr['day']} 23:59:00"))->format('Y-m-d H:i:s');

        if ($startDate > $endDate) {
            $flash->addMessage('error', "Tanggal tidak valid! Silahkan dicek kembali...");

            return $response->withRedirect($router->pathFor('profit.index'));
        } 
        
        $receipts = Receipt::whereBetween('settled_date', [$startDate, $endDate])
                    ->where('is_deleted', 0)
                    ->orderBy('settled_date', 'asc')
                    ->paginate(20)
                    ->appends($request->getParams());
        $omset = Receipt::whereBetween('settled_date', [$startDate, $endDate])
                ->where('is_deleted', 0)
                ->sum('total');

        return $twig->render($response, 'calculations/profit/index.twig', compact('receipts', 'omset'));
    }
}