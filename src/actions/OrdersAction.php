<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use lbs\order\services\OrderServices;
use \lbs\order\services\utils\FormatterAPI;

final class OrdersAction
{
  public function __invoke(
    Request $rq,
    Response $rs
  ): Response {
    $query = $rq->getQueryParams();
    $os = new OrderServices();

    $page = 0;
    $sizePage = 10;

    if (isset($query['page']) && $query['page'] > 0) {
      $page = $query['page'];
    }

    // if (isset($query['size']) && $query['size'] > 0) {
    //   $sizePage = $query['size'];
    // }

    try {

      $countOrders = $os->getCountOrders();

      $lastPage = ceil($countOrders / $sizePage) - 1;

      if ($page > $lastPage) {
        $page = $lastPage;
      }


      $orders = $os->getOrders($page, $sizePage);
    } catch (RessourceNotFoundException $e) {
      throw new HttpNotFoundException($rq, $e->getMessage());
    }
    $data = [
      'type' => 'collection',
      'count' => $countOrders,
      'size' => count($orders),
      'orders' => $orders
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data);
  }
}
