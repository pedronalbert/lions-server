<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model {
  protected $fillable = ['type', 'available', 'using', 'damaged'];

  protected $guarded = ['id'];

  public function backToInventory($amount) {
    $this->available += $amount;
    $this->using -= $amount;
    $this->save();
  }
}
