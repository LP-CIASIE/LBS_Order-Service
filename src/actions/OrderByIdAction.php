<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use lbs\order\services\OrderServices;
use Slim\Routing\RouteContext;

use \lbs\order\services\utils\FormatterAPI;

final class OrderByIdAction extends AbstractAction
{
  public function __invoke(
    Request $rq,
    Response $rs,
    array $args
  ): Response {
    $query = $rq->getQueryParams();
    $os = $this->container->get('order.service');
    try {

      if (isset($query['embed'])) {
        $order = $os->getOrderById($args['id'], $query['embed']);
      } else {
        $order = $os->getOrderById($args['id']);
      }
    } catch (RessourceNotFoundException $e) {
      throw new HttpNotFoundException($rq, $e->getMessage());
    }

    $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

    $data = [
      'type' => 'resource',
      'order' => $order,
      'links' => [
        'items' => [
          'href' => $routeParser->urlFor('ordersItems', ['id' => $order['id']])
        ],
        'self' => [
          'href' => $routeParser->urlFor('ordersById', ['id' => $order['id']],)
        ],
      ]
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data, 200);
  }
}
