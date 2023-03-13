<?php

namespace lbs\order\errors\renderer;

class JsonErrorRenderer extends \Slim\Error\Renderers\JsonErrorRenderer
{
  public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
  {
    $data = [
      'type' => 'error',
      'error' => $exception->getCode(),
      'message' => $exception->getMessage(),
    ];

    if ($displayErrorDetails) $data['details'] = [
      'file' => $exception->getFile(),
      'line' => $exception->getLine(),
      'trace' => $exception->getTraceAsString()
    ];

    return json_encode($data, JSON_PRETTY_PRINT);
  }
}
