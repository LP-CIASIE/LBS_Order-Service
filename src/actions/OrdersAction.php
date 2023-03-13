<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use Slim\Routing\RouteContext;

use lbs\order\services\OrderServices;
use \lbs\order\services\utils\FormatterAPI;

final class OrdersAction
{
  public function __invoke(
    Request $rq,
    Response $rs
  ): Response {
    $query = $rq->getQueryParams();
    $query = $rq->getQueryParams();
    $os = new OrderServices();
    $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

    $page = 0;
    $queryPage = 1;
    $sizePage = 10;

    if (isset($query['page']) && $query['page'] > 0) {
      $queryPage = $query['page'];
      $page = $query['page'] - 1;
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
      'links' => [
        'self' => [
          'href' => $routeParser->urlFor('orders', [], ['page' => $queryPage])
        ]
      ],
      'orders' => $orders,
    ];

    if ($page + 1 <= $lastPage) {
      $data['links']['next']['href'] = $routeParser->urlFor('orders', [], ['page' => $queryPage + 1]);
    }

    if ($page - 1 >= 0) {
      $data['links']['prev']['href'] = $routeParser->urlFor('orders', [], ['page' => $queryPage - 1]);
    }

    $data['links']['last']['href'] = $routeParser->urlFor('orders', [], ['page' => $lastPage + 1]);
    $data['links']['first']['href'] = $routeParser->urlFor('orders', [], ['page' => 1]);


    return FormatterAPI::formatResponse($rq, $rs, $data);
  }
}
