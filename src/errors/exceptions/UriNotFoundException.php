<?php

namespace lbs\order\errors\exceptions;

use \Slim\Exception\HttpNotFoundException;

class UriNotFoundException extends HttpNotFoundException
{
  protected $code = 404;
  protected $message = 'URI non trouvée.';
  protected string $title = '404 - URI non trouvée';
  protected string $description = 'L\'URI demandée n\'a pas été trouvée. Veuillez vérifier l\'URI et réessayer.';
}
