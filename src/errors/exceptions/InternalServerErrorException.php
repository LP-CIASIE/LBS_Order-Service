<?php

namespace lbs\order\errors\exceptions;

use Slim\Exception\HttpInternalServerErrorException;

class InternalServerErrorException extends HttpInternalServerErrorException
{
  protected $code = 500;
  protected $message = 'Erreur interne du serveur.';
  protected string $title = '500 - Erreur interne du serveur';
  protected string $description = 'Une erreur interne du serveur est survenue. Veuillez réessayer ultérieurement.';
}
