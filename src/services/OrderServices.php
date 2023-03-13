<?php

namespace lbs\order\services;

use lbs\order\models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\order\errors\exceptions\BodyErrorValidationException;
use lbs\order\errors\exceptions\BodyMissingAttributesException;
use lbs\order\errors\exceptions\RessourceNotFoundException;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as Validator;

final class OrderServices
{
  public function getOrders(): array
  {
    return models\Commande::select([
      'id',
      'mail as client_mail',
      'created_at as order_date',
      'montant as total_amount'
    ])->get()->toArray();
  }

  public function getOrderById($id): ?array
  {
    try {
      $order = models\Commande::select([
        'id',
        'mail as client_mail',
        'nom as client_name',
        'created_at as order_date',
        'livraison as delivery_date',
        'montant as total_amount'
      ])->findOrFail($id);
    } catch (ModelNotFoundException $e) {
      throw new RessourceNotFoundException("Ressource non trouvée.");
    }

    return $order->toArray();
  }

  public function updateOrder($id, $body): ?array
  {
    try {
      $order = models\Commande::findOrFail($id);
    } catch (ModelNotFoundException $e) {
      throw new RessourceNotFoundException("Ressource non trouvée.");
    }

    if (!isset($body['client_name'], $body['client_mail'], $body['delivery_date'])) {
      throw new BodyMissingAttributesException();
    }

    try {
      Validator::key('client_name', Validator::stringType()->notEmpty())
        ->key('client_mail', Validator::email())
        ->key('delivery_date', Validator::dateTime('Y-m-d H:i:s'))
        ->assert($body);
    } catch (NestedValidationException $e) {
      throw new BodyErrorValidationException();
    }

    $order->nom = $body['client_name'];
    $order->mail = $body['client_mail'];
    $order->livraison = $body['delivery_date'];

    try {
      $order->save();
    } catch (\Exception $e) {
      throw new \Exception("Erreur lors de la mise à jour de la commande.");
    }

    return $order->toArray();
  }
}
