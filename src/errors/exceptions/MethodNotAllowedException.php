<?php

namespace lbs\order\errors\exceptions;

use Slim\Exception\HttpMethodNotAllowedException;

class MethodNotAllowedException extends HttpMethodNotAllowedException
{
  protected $code = 405;
  protected $message = 'La méthode demandée n\'est pas supportée. Veuillez vérifier l\'URI et réessayer.';
  protected string $title = '405 - Méthode non authorisée';
}
