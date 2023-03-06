<?php

namespace lbs\order\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\services\OrderServices;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use \lbs\order\services\utils\FormatterAPI;

final class OrderItemsAction
{
  public function __invoke(
    Request $rq,
    Response $rs,
    array $args
  ): Response {
    $os = new OrderServices();
    try {
      $order = $os->getItemsOrder($args['id']);
      var_dump($args);
    } catch (RessourceNotFoundException $e) {
      throw new HttpNotFoundException($rq, $e->getMessage());
    }
    $data = [
      'type' => 'resource',
      'order' => $order
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data);
  }
}
