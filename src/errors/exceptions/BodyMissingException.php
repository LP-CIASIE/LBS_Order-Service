<?php

namespace lbs\order\errors\exceptions;

use Exception;
use Slim\Exception\HttpInternalServerErrorException;

class BodyMissingException extends Exception
{
  protected $code = 400;
  protected $message = 'Le body de la requête n\'a pas été trouvé, veuillez recommencer.';
  protected string $title = '400 - Body introuvable.';
}
