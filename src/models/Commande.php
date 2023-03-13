<?php

namespace lbs\order\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Commande extends Model
{
  use HasUuids;

  protected $table = 'commande';
  protected $primaryKey = 'id';
  public $timestamps = true;
  public $incrementing = false;
  protected $keyType = 'string';


  public function items()
  {
    return $this->hasMany(Item::class, 'command_id', 'id');
  }
}
