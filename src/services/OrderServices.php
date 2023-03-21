<?php

namespace lbs\order\services;

use lbs\order\models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\order\errors\exceptions\BodyErrorValidationException;
use lbs\order\errors\exceptions\BodyMissingAttributesException;
use lbs\order\errors\exceptions\RessourceNotFoundException;

use Respect\Validation\Exceptions\NestedValidationException;
use Illuminate\Support\Str;
use lbs\order\models\Item;
use Respect\Validation\Validator as Validator;

final class OrderServices
{

  public function getOrders(int $page = 0, int $sizePage = 10): array
  {
    return models\Commande::select([
      'id',
      'mail as client_mail',
      'created_at as order_date',
      'montant as total_amount'
    ])->offset($page * $sizePage)->limit($sizePage)->get()->toArray();
  }

  public function getCountOrders(): int
  {
    return models\Commande::count();
  }

  public function getOrderById($id, bool | string $embed = false): ?array
  {
    try {
      $order = models\Commande::select([
        'id',
        'mail as client_mail',
        'nom as client_name',
        'created_at as order_date',
        'livraison as delivery',
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

  public function newOrder($body): ?array
  {
    $order = new models\Commande;

    if (!isset($body['client_name'], $body['client_mail'], $body['delivery'])) {
      throw new BodyMissingAttributesException();
    }

    try {
      Validator::key('client_name', Validator::stringType()->notEmpty())
        ->key('client_mail', Validator::email())
        ->key('delivery', Validator::key('date', Validator::Date('d-m-Y'))
          ->key('time', Validator::Time('H:i')))
        ->key('items', Validator::each(Validator::key('uri', Validator::stringType()->notEmpty()->noWhitespace())
          ->key('name', Validator::stringType()->notEmpty())
          ->key('price', Validator::numericVal())
          ->key('q', Validator::intType()->min(1))))
        ->assert($body);
    } catch (NestedValidationException $e) {
      throw new BodyErrorValidationException();
    }

    $order->id = Str::uuid();
    $order->nom = $body['client_name'];
    $order->mail = $body['client_mail'];
    $order->livraison = date('Y-m-d H:i:s', strtotime("{$body['delivery']['date']} {$body['delivery']['time']}"));
    $order->montant = 0;

    try {

      foreach ($body['items'] as $key => $rq_item) {
        $order->montant += ($rq_item['q'] * $rq_item['price']);

        $item = new Item();
        $item->uri = $rq_item['uri'];
        $item->libelle = $rq_item['name'];
        $item->tarif = $rq_item['price'];
        $item->quantite = $rq_item['q'];

        $order->items()->save($item);
      }

      $order->save();
    } catch (\Exception $e) {
      echo $e->getMessage();
      throw new \Exception("Erreur lors de l'enregistrement de la commande.");
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

    if (!isset($body['client_name'], $body['client_mail'], $body['delivery'])) {
      throw new BodyMissingAttributesException();
    }

    try {
      Validator::key('client_name', Validator::stringType()->notEmpty())
        ->key('client_mail', Validator::email())
        ->key('delivery', Validator::key('date', Validator::Date('d-m-Y'))
          ->key('time', Validator::Time('H:i')))
        ->assert($body);
    } catch (NestedValidationException $e) {
      throw new BodyErrorValidationException();
    }

    $order->nom = $body['client_name'];
    $order->mail = $body['client_mail'];
    $order->livraison = date('Y-m-d H:i:s', strtotime("{$body['delivery']['date']} {$body['delivery']['time']}"));

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
}
