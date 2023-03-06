<?php

namespace lbs\order\models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $table = 'item';
  protected $primaryKey = 'id';
  public $timestamps = false;
}
