<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Resource;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Logger;

class ResourceController extends Controller {
  private $notLoginJson = ['message' => 'No has iniciado session!'];
  private $needPermissionsJson = ['message' => 'No tiene los permisos necesarios'];
  private $resourceNotFound = ['message' => 'Recurso no encontrado'];

  private $validatorMessages = [
    'type.unique' => 'Recurso ya ha sido registrado',
    'available.integer' => 'Disponibles debe ser un número',
    'damaged.integer' => 'Dañados debe ser un número'
  ];

  public function index(Request $request) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('user')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $resources = Resource::all();

    return response()->json($resources);
  }

  public function store(Request $request) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $data = $request->only('type', 'available', 'using', 'damaged');
    $validatorRules = [
      'type' => 'unique:resources',
      'available' => 'integer',
      'using' => 'integer',
      'damaged' => 'integer'
    ];
    $validator = Validator::make($data, $validatorRules, $this->validatorMessages);

    if($validator->fails()) {
      $errorsMessages = $validator->errors()->all();

      return response()->json(['message' => $errorsMessages[0]], 400);
    } else {
      $newResource = Resource::create($data);

      $logMessage = 'El usuario '.$user->name.' ha registrado un nuevo recurso id: '.$newResource->id;
      Logger::create(['message' => $logMessage]);

      return response()->json($newResource);   
    }
  }

  public function show(Request $request, $id)  {
    $resource = Resource::find($id);

    if($resource) {
      return response()->json($resource);
    } else {
      return response()->json($this->resourceNotFound, 404);
    }
  }

  public function update(Request $request, $id) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $resource = Resource::find($id);

    if($resource) {
      $data = $request->only('type', 'available', 'using', 'damaged');
      $validatorRules = [
        'type' => 'unique:resources,type,'.$resource->id,
        'available' => 'integer',
        'using' => 'integer',
        'damaged' => 'integer'
      ];
      $validator = Validator::make($data, $validatorRules, $this->validatorMessages);

      if($validator->fails()) {
        $errorsMessages = $validator->errors()->all();

        return response()->json(['message' => $errorsMessages[0]], 400);
      } else {
        $resource->update($data);

        $logMessage = 'El usuario '.$user->name.' ha actualizado el recurso id: '.$resource->id;
        Logger::create(['message' => $logMessage]);

        return response()->json($resource);   
      }

    } else {
      return response()->json($this->resourceNotFound, 404);
    }
  }

  public function destroy($id) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $resource = Resource::find($id);

    if($resource) {
      $logMessage = 'El usuario '.$user->name.' ha eliminado el recurso id: '.$resource->id;
      Logger::create(['message' => $logMessage]);

      $resource->delete();

      return response()->json(['message' => 'Recurso ha sido eliminado']);
    } else {
      return response()->json($this->resourceNotFound, 404);
    }

  }
}
