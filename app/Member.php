<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;

class Member extends Model
{
  protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'ci'];

  private $validatorMessages = [
    'first_name.required'=> 'El nombre es obligatorio',
    'last_name.required'=> 'El apellido es obligatorio',
    'ci.required'=> 'La cedula es obligatoria',
    'ci.unique'=> 'Ya existe un miembro con esta cedula'
  ];


  public function events() {
    return $this->belongsToMany('App\Event');
  }

  public function validate($data) {
    $validatorRules =  [
      'first_name'=> 'required',
      'last_name'=> 'required',
      'ci'=> 'required|unique:members'
    ];

    $validator = Validator::make($data, $validatorRules, $validatorMessages);

    if($validator->fails()) {
      $this->errors = $validator->errors()->all();

      return false;
    } else {
      return true;
    }
  }
}
