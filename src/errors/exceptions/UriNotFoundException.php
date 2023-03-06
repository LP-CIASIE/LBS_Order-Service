<?php

namespace lbs\order\errors\exceptions;

use \Slim\Exception\HttpNotFoundException;

class UriNotFoundException extends HttpNotFoundException
{
  protected $code = 404;
  protected $message = 'L\'URI demandée n\'a pas été trouvée. Veuillez vérifier l\'URI et réessayer.';
  protected string $title = '404 - URI non trouvée';
}
