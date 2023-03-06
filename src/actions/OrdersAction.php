<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\services\OrderServices;
use \lbs\order\services\utils\FormatterAPI;

final class OrdersAction
{
  public function __invoke(
    Request $rq,
    Response $rs
  ): Response {
    $os = new OrderServices();
    $orders = $os->getOrders();

    $data = [
      'type' => 'collection',
      'count' => count($orders),
      'orders' => $orders
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data);
  }
}
