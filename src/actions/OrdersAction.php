<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use Slim\Routing\RouteContext;

use lbs\order\services\OrderServices;
use \lbs\order\services\utils\FormatterAPI;

final class OrdersAction extends AbstractAction
{
  public function __invoke(
    Request $rq,
    Response $rs
  ): Response {

    $query = $rq->getQueryParams();
    $os = $this->container->get('order.service');
    $routeParser = RouteContext::fromRequest($rq)->getRouteParser();
    
    $page = 0;
    $queryPage = 1;
    $sizePage = 10;
    $c = '';
    $sort = '';

    if (isset($query['page']) && $query['page'] > 0) {
      $queryPage = $query['page'];
      $page = $query['page'] - 1;
    }

    if (isset($query['c']) && is_string($query['c']) && !empty($query['c'])) {
      $c = $query['c'];
    }

    if (isset($query['sort']) && is_string($query['sort']) && !empty($query['sort'])) {
      $sort = $query['sort'];
    }

    try {
      $countOrders = $os->getCountOrders($c);
      $lastPage = ceil($countOrders / $sizePage) - 1;

      if ($page > $lastPage) {
        $page = $lastPage;
      }

      $orders = $os->getOrders($page, $sizePage, $c, $sort);
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
    
    // if (isset($query['size']) && $query['size'] > 0) {
    //   $sizePage = $query['size'];
    // }

    return FormatterAPI::formatResponse($rq, $rs, $data, 200);
  }
}
