<?php

namespace lbs\order\errors\renderer;

use Slim\Exception\HttpException;

class JsonErrorRenderer extends \Slim\Error\Renderers\JsonErrorRenderer
{
  public function __invoke(HttpException $exception, bool $displayErrorDetails): string
  {
    $data = [
      'type' => 'error',
      'error' => $exception->getCode(),
      'message' => $exception->getMessage(),
      'description' => $exception->getDescription()
    ];
    if ($displayErrorDetails) $data['details'] = [
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
      'trace' => $exception->getTraceAsString()
    ];

    return json_encode($data, JSON_PRETTY_PRINT);
  }
}
