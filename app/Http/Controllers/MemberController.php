<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Member;
use Auth;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberController extends Controller {
  private $notLoginJson = ['message' => 'No has iniciado session!'];
  private $needPermissionsJson = ['message' => 'No tiene los permisos necesarios'];
  private $memberNotFoundJson = ['message' => 'Miembro no encontrado'];

  private $validatorMessages = [
    'first_name.required'=> 'El nombre es obligatorio',
    'last_name.required'=> 'El apellido es obligatorio',
    'ci.required'=> 'La cedula es obligatoria',
    'ci.unique'=> 'Ya existe un miembro con esta cedula'
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

    $members = Member::all();

    return response()->json($members);
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

    $data = $request->only('first_name', 'last_name', 'email', 'phone', 'ci');
    $validatorRules =  [
      'first_name'=> 'required',
      'last_name'=> 'required',
      'ci'=> 'required|unique:members'
    ];
    $validator = Validator::make($data, $validatorRules, $this->validatorMessages);

    if($validator->fails()) {
      $errorsMessages = $validator->errors()->all();

      return response()->json(['message' => $errorsMessages[0]], 400);
    } else {
      $newMember = Member::create($data);

      return response()->json($newMember);   
    }
  }

  public function show(Request $request, $id) {
    $member = Member::find($id);

    if($member) {
      return response()->json($member);
    } else {
      return response()->json($this->memberNotFoundJson, 404);
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

    $member = Member::find($id);

    if($member) {
      $data = $request->only('first_name', 'last_name', 'email', 'phone', 'ci');
      $validatorRules =  [
        'first_name'=> 'required',
        'last_name'=> 'required',
        'ci'=> 'required|unique:members,ci,'.$member->id
      ];
      $validator = Validator::make($data, $validatorRules, $this->validatorMessages);

      if($validator->fails()) {
        $errorsMessages = $validator->errors()->all();

        return response()->json(['message' => $errorsMessages[0]], 400);
      } else {
        $member->update($data);

        return response()->json($member);   
      }
    } else {
      return response()->json($this->memberNotFoundJson, 404);
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

    $member = Member::find($id);

    if($member) {
      $member->delete();

      return response()->json(['message' => 'Miembro ha sido eliminado exitosamente']);
    } else {
      return response()->json($this->memberNotFoundJson, 404);
    }
  }
}
