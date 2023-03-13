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

  public function getOrderById($id, bool | string $embed = false): ?array
  {
    try {
      $order = models\Commande::select([
        'id',
        'mail as client_mail',
        'nom as client_name',
        'created_at as order_date',
        'livraison as delivery_date',
        'montant as total_amount'
      ])->where('id', '=', $id);

      if ($embed) {
        switch ($embed) {
          case 'items':
            $order = $order->with('items');
            break;

          default:
            # code...
            break;
        }
      }


      $order = $order->findOrFail($id);
    } catch (ModelNotFoundException $e) {
      throw new RessourceNotFoundException("Ressource non trouvée.");
    }

    return $order->toArray();
  }

  public function getItemsOrder($id)
  {
    try {
      $order = models\Commande::select()->findOrFail($id);
      $items = $order->items()->get();
    } catch (ModelNotFoundException $e) {
      throw new RessourceNotFoundException("Ressource non trouvée : " . $e);
    }
    return $items->toArray();
  }
}
