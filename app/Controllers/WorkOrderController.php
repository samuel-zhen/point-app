<?php

namespace Point\Controllers;

use Slim\Router;
use Point\Helpers;
use Point\Auth\Auth;
use Slim\Views\Twig;
use Slim\Flash\Messages;
use Point\Models\WorkOrder;
use Point\Validation\Validator;
use Respect\Validation\Validator as v;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WorkOrderController
{
    public function ongoing(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q_customer']) && isset($_GET['q_print'])) {
            $customer = trim($_GET['q_customer']);
            $print_type = trim($_GET['q_print']);
            $workOrders = WorkOrder::where('is_complete', 0)
                        ->where('customer', $customer)
                        ->where('print_type', 'like', '%' . $print_type . '%')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20)
                        ->appends(['q_customer' => $customer, 'q_print' => $print_type]);
        } else {
            $workOrders = WorkOrder::where('is_complete', 0)->orderBy('created_at', 'desc')->paginate(20);
        }
        return $twig->render($response, 'work-order/ongoing.twig', compact('workOrders'));
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        return $twig->render($response, 'work-order/create.twig');
    }

    public function completed(ServerRequestInterface $request, ResponseInterface $response, Twig $twig)
    {
        if (isset($_GET['q_customer']) && isset($_GET['q_print'])) {
            $customer = trim($_GET['q_customer']);
            $print_type = trim($_GET['q_print']);
            $workOrders = WorkOrder::where('is_complete', 1)
                        ->where('customer', $customer)
                        ->where('print_type', 'like', '%' . $print_type . '%')
                        ->orderBy('created_at', 'desc')
                        ->paginate(20)
                        ->appends(['q_customer' => $customer, 'q_print' => $print_type]);
        } else {
            $workOrders = WorkOrder::where('is_complete', 1)->orderBy('created_at', 'desc')->paginate(20);
        }
        return $twig->render($response, 'work-order/completed.twig', compact('workOrders'));
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (WorkOrder::find($id)) {
            $workOrder = WorkOrder::find($id);
            return $twig->render($response, 'work-order/show.twig', compact('workOrder'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (WorkOrder::find($id)) {
            $workOrder = WorkOrder::find($id);
            return $twig->render($response, 'work-order/edit.twig', compact('workOrder'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, Auth $auth)
    {
        $validation = $validator->validate($request, [
            'customer_name' => v::notEmpty(),
            'print_type' => v::notEmpty(),
            'quantity' => v::notEmpty()->numeric(),
            'unit' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('workOrders.create'));
        }

        $workOrder = WorkOrder::create([
            'customer' => Helpers::emptyToNull(($request->getParam('customer_name'))),
            'user_id' => $auth->user()->id,
            'print_type' => Helpers::emptyToNull(($request->getParam('print_type'))),
            'quantity' => Helpers::thousandFormat($request->getParam('quantity')),
            'unit' => Helpers::emptyToNull(($request->getParam('unit'))),
            'ink' => Helpers::emptyToNull(($request->getParam('ink'))),
            'type_color' => Helpers::emptyToNull(($request->getParam('type_color'))),
            'dimension' => Helpers::emptyToNull(($request->getParam('dimension'))),
            'set_double' => Helpers::emptyToNull(($request->getParam('set_double'))),
            'number' => Helpers::emptyToNull(($request->getParam('number'))),
            'holes' => Helpers::emptyToNull(($request->getParam('holes'))),
            'glue' => Helpers::emptyToNull(($request->getParam('glue'))),
        ]);

        $flash->addMessage('success', 'SPK berhasil disimpan!');

        return $response->withRedirect($router->pathFor('workOrders.show', ['id' => $workOrder->id]));
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, Router $router, Validator $validator, Messages $flash, Auth $auth, $id)
    {
        $validation = $validator->validate($request, [
            'customer_name' => v::notEmpty(),
            'print_type' => v::notEmpty(),
            'quantity' => v::notEmpty()->numeric(),
            'unit' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($router->pathFor('workOrders.edit', ['id' => $id]));
        }

        $workOrder = WorkOrder::find($id)->update([
            'customer' => Helpers::emptyToNull(($request->getParam('customer_name'))),
            'user_id' => $auth->user()->id,
            'print_type' => Helpers::emptyToNull(($request->getParam('print_type'))),
            'quantity' => Helpers::thousandFormat($request->getParam('quantity')),
            'unit' => Helpers::emptyToNull(($request->getParam('unit'))),
            'ink' => Helpers::emptyToNull(($request->getParam('ink'))),
            'type_color' => Helpers::emptyToNull(($request->getParam('type_color'))),
            'dimension' => Helpers::emptyToNull(($request->getParam('dimension'))),
            'set_double' => Helpers::emptyToNull(($request->getParam('set_double'))),
            'number' => Helpers::emptyToNull(($request->getParam('number'))),
            'holes' => Helpers::emptyToNull(($request->getParam('holes'))),
            'glue' => Helpers::emptyToNull(($request->getParam('glue'))),
        ]);

        $flash->addMessage('success', 'SPK berhasil diubah!');

        return $response->withRedirect($router->pathFor('workOrders.show', ['id' => $id]));
    }

    public function isCompleted(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, Auth $auth, $id)
    {
        if (WorkOrder::find($id)) {
            $workOrder = WorkOrder::find($id);
            $workOrder->update([
                'is_complete' => 1,
                'user_id' => $auth->user()->id,
            ]);

            $flash->addMessage('success', 'Status SPK berhasil diubah!');

            return $response->withRedirect($router->pathFor('workOrders.show', ['id' => $id]));
        }
    }
   
    public function isOngoing(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, Auth $auth, $id)
    {
        if (WorkOrder::find($id)) {
            $workOrder = WorkOrder::find($id);
            $workOrder->update([
                'is_complete' => 0,
                'user_id' => $auth->user()->id,
            ]);

            $flash->addMessage('success', 'Status SPK berhasil diubah!');

            return $response->withRedirect($router->pathFor('workOrders.show', ['id' => $id]));
        }
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, Router $router, Messages $flash, $id)
    {
        if (WorkOrder::find($id)) {
            $workOrder = WorkOrder::destroy($id);

            $flash->addMessage('success', 'SPK berhasil dihapus!');
    
            return $response->withRedirect($router->pathFor('workOrders.ongoing'));
        }
    }

    public function print(ServerRequestInterface $request, ResponseInterface $response, Twig $twig, $id)
    {
        if (WorkOrder::find($id)) {
            $workOrder = WorkOrder::find($id);

            return $twig->render($response, 'work-order/print.twig', compact('workOrder'));
        }

        throw new \Slim\Exception\NotFoundException($request, $response);
    }
}