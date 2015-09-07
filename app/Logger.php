<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

class Logger extends Model {
  protected $table = 'logs';

  protected $fillable = ['message'];
  
}
