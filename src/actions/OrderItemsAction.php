<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\services\OrderServices;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use \lbs\order\services\utils\FormatterAPI;


use Slim\Routing\RouteContext;

final class OrderItemsAction
{
  public function __invoke(
    Request $rq,
    Response $rs,
    array $args
  ): Response {
    $os = new OrderServices();

    $routeParser = RouteContext::fromRequest($rq)->getRouteParser();

    try {
      $items = $os->getItemsOrder($args['id']);
    } catch (RessourceNotFoundException $e) {
      throw new HttpNotFoundException($rq, $e->getMessage());
    }
    $data = [
      'type' => 'collection',
      'items' => $items,
      'links' => [
        'self' => [
          'href' => $routeParser->urlFor('ordersItems', ['id' => $args['id']]),
        ],
        'commande' => [
          'href' => $routeParser->urlFor('ordersById', ['id' => $args['id']]),
        ]
      ]
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data, 200);
  }
}
