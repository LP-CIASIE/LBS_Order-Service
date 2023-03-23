<?php

namespace lbs\order\services;

use lbs\order\models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\order\errors\exceptions\BodyErrorValidationException;
use lbs\order\errors\exceptions\BodyMissingAttributesException;
use lbs\order\errors\exceptions\RessourceNotFoundException;
use lbs\order\models\Commande;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as Validator;

final class OrderServices
{

  public function getOrders(int $page, int $sizePage, string $c, string $sort): array
  {
    $query = models\Commande::select([
      'id',
      'mail as client_mail',
      'created_at as order_date',
      'montant as total_amount'
    ]);
    if(isset($c) && !empty($c)){
      $query = $query->where('mail', '=', $c);
    }

    $query = $query->offset($page * $sizePage)->limit($sizePage);

    if(isset($sort) && !empty($sort)){
      switch ($sort) {
        case 'date':
          $query = $query->orderBy('created_at', 'desc');
          break;
        case 'amount':
          $query = $query->orderBy('montant', 'desc');
          break;
      }
    }

    return $query->offset($page * $sizePage)->limit($sizePage)->get()->toArray();
  }

  public function getCountOrders($c): int
  {
    $query = models\Commande::select();

    if(isset($c) && !empty($c)){
      $query = $query->where('mail', '=', $c);
    }

    return $query->count();
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

  public function getItemsOrder($id)
  {
    try {
      $order = models\Commande::findOrFail($id);
      $items = $order->items()->get();
    } catch (ModelNotFoundException $e) {
      throw new RessourceNotFoundException("Ressource non trouvée : " . $e);
    }

    return $items->toArray();
  }

  public function getOrdersByClient($c)
  {
    try{
      $client_orders = Commande::select()->where('client_mail','=', $c);
    } catch (ModelNotFoundException $e){
      throw new RessourceNotFoundException("Ressource non trouvée : " .$e);
    }
    
    return $client_orders->toArray();
  }
}
