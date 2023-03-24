<?php

namespace lbs\order\actions;

use lbs\order\errors\exceptions\BodyMissingException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\services\OrderServices;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use \lbs\order\services\utils\FormatterAPI;
use Slim\Routing\RouteContext;

final class NewOrderAction extends AbstractAction
{
  public function __invoke(Request $rq, Response $rs, array $args): Response
  {
    $body = $rq->getParsedBody() ?? null;
    if (is_null($body)) {
      throw new BodyMissingException();
    }

    $os = $this->container->get('order.service');

    try {
      $order = $os->newOrder($body);
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

    $rs = $rs->withHeader('Location', $routeParser->urlFor('ordersById', ['id' => $order['id']]));

    return FormatterAPI::formatResponse($rq, $rs, $data, 201);
  }
}
