<?php

namespace lbs\order\services;

use lbs\order\models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\order\errors\exceptions\RessourceNotFoundException;

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

  public function getItemsOrder($id)
  {
    try {
    } catch (ModelNotFoundException $e) {
      throw new RessourceNotFoundException("Ressource non trouvée.");
    }
  }
}
