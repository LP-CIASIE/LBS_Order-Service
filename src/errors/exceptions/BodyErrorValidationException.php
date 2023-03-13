<?php

namespace lbs\order\errors\exceptions;

use Exception;

class BodyErrorValidationException extends Exception
{
  protected $code = 400;
  protected $message = 'Certain attributs ne sont pas conforme.';
  protected string $title = '400 - Error Validation Attribut.';
}
