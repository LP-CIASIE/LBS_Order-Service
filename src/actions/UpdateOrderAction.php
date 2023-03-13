<?php

namespace lbs\order\actions;

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
    $os = new OrderServices();

    $data = [
      'type' => 'resource',
      'order' => 'TODO'
    ];

    return FormatterAPI::formatResponse($rq, $rs, $data);
  }
}
