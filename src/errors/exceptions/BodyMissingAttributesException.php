<?php

namespace lbs\order\errors\exceptions;

use Exception;

class BodyMissingAttributesException extends Exception
{
  protected $code = 400;
  protected $message = 'Certain attributs du body de la requête sont manquants.';
  protected string $title = '400 - Missing Attribut.';
}
