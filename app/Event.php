<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Logger;

class Event extends Model
{
  public $errorMessage;

  protected $fillable = ['title', 'description', 'location', 'sector', 'date', 'active'];

  public function members() {
    return $this->belongsToMany('App\Member', 'events_members');
  }

  public function addResource($data) {
    $resource = Resource::find($data['resource_id']);
    $member = Member::find($data['member_id']);

    if(!$resource) {
      $this->errorMessage = 'Recurso no encontrado';
      return false;
    }

    if ($resource->available < $data['amount']) {
      $this->errorMessage = 'No hay suficientes recursos';
      return false;
    }

    //check if resource exist
    $eventResource = DB::table('events_resources')
      ->where('resource_id', $data['resource_id'])
      ->where('event_id', $data['event_id'])
      ->first();

    if ($eventResource) {
      $newAmount = $eventResource->amount + $data['amount'];

      DB::table('events_resources')
        ->where('id', $eventResource->id)
        ->update(['amount' => $newAmount]);

    } else {
      DB::table('events_resources')->insert($data);
    }

    $eventResource = DB::table('events_resources')
      ->where('resource_id', $data['resource_id'])
      ->where('event_id', $data['event_id'])
      ->first();

    $eventResource->type = $resource->type;

    $resource->available -= $data['amount'];
    $resource->using += $data['amount'];
    $resource->save();

    return $eventResource;
  }

  public function removeResource($data) {
    $resource = Resource::find($data['resource_id']);

    $eventResource = DB::table('events_resources')
      ->where('resource_id', $data['resource_id'])
      ->where('event_id', $data['event_id'])
      ->first();

    if($eventResource && $resource) {
      $resource->available += $eventResource->amount;
      $resource->using -= $eventResource->amount;

      DB::table('events_resources')
        ->where('id', $eventResource->id)
        ->delete();
      $resource->save();

      return true;
    } else {
      return false;
    }

  }

  public function loadResources() {
    $resources = DB::table('events_resources')
      ->where('event_id', $this->id)
      ->get();

    foreach ($resources as $resource) {
      if ($resource->member_id >= 1) {
        if($member = Member::find($resource->member_id)) {
          $resource->member = $member->first_name." ".$member->last_name;
        } else {
          $resource->member = "Desconocido";
        }
      } else {
        $resource->member = "Ninguno";
      }

      $joinResource = Resource::find($resource->resource_id);

      if ($joinResource) {
        $resource->type = $joinResource->type;
      } else {
        $resource->type = "Desconocido";
      }
    }

    $this->resources = $resources;
  }

  public function finishEvent() {
    $resources = DB::table('events_resources')
      ->where('event_id', $this->id)
      ->get();

    foreach ($resources as $resource) {
      $resourceRecord = Resource::find($resource->resource_id);

      if($resourceRecord) {
        $resourceRecord->backToInventory($resource->amount);
      }
    }

    $this->active = 0;
    $this->save();
  }
}
