<?php

namespace lbs\order\actions;

use lbs\order\errors\exceptions\BodyMissingException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use lbs\order\services\OrderServices;

use lbs\order\errors\exceptions\RessourceNotFoundException;
use Slim\Exception\HttpNotFoundException;

use \lbs\order\services\utils\FormatterAPI;

final class UpdateOrderAction
{
  public function __invoke(Request $rq, Response $rs, array $args): Response
  {
    $body = $rq->getParsedBody();
    if (!isset($body)) {
      throw new BodyMissingException();
    }

    $os = new OrderServices();

    $os->updateOrder($args['id'], $body);

    $data = [
      'type' => 'success',
      'result' => 'ok'
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data);
  }
}
