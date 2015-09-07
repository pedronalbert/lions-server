<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use App\Role;
use App\Logger;

class UserController extends Controller {
  private $notLoginJson = ['message' => 'No has iniciado session!'];
  private $needPermissionsJson = ['message' => 'No tiene los permisos necesarios'];
  private $userNotFoundJson = ['message' => 'Usuario no encontrado'];
  private $validatorMessages = [
    'name.required' => 'El nombre es obligatorio',
    'email.required' => 'El correo es obligatorio',
    'email.email' => 'Debe ingresar un correo valido',
    'email.unique' => 'Ya existe un usuario registrado con ese correo',
    'password.required' => 'La contraseña es obligatoria'
  ];

  public function index() {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $users = User::all();
    foreach ($users as $user) {
      $user->loadRole();
    }

    return response()->json($users);
  }

  public function show(Request $request, $userId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $user = User::find($userId);

    if($user) {
      $user->loadRole();

      return response()->json($user);
    } else {
      return response()->json($this->userNotFoundJson, 404);
    }
  }

  public function logged() {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();
      $user->loadRole();

      return response()->json($user);
    }
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

    $data = $request->only('name', 'email', 'password');
    $role = $request->input('role');

    $validatorRules = [
      'name' => 'required',
      'email' => 'required|email|unique:users',
      'password' => 'required',
    ];
    $validator = Validator::make($data, $validatorRules, $this->validatorMessages);

    if($validator->fails()) {
      $errorsMessages = $validator->errors()->all();

      return response()->json(['message' => $errorsMessages[0]], 400);
    } else {
      $newUser = User::create([
          'name' => $data['name'],
          'email' => $data['email'],
          'password' => bcrypt($data['password']),
      ]);

      if($role == 1) {
        $newUser->roles()->attach(1);
        $newUser->roles()->attach(2);
      } else {
        $newUser->roles()->attach(2);
      }

      $newUser->loadRole();

      $logMessage = 'El usuario '.$user->name.' ha registrado al usuario id: '.$newUser->id;
      Logger::create(['message' => $logMessage]);

      return response()->json($newUser);
    }
  }

  public function login(Request $request) {
    $data = $request->only('email', 'password');

    if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
      $user = Auth::user();
      // Authentication passed...
      $logMessage = 'El usuario '.$user->name.' ha iniciado session';
      Logger::create(['message' => $logMessage]);

      return response()->json(['message' => 'Login Success'], 200);
    } else {
      return response()->json(['message' => 'Los datos ingresados son incorrectos'], 400);
    }
  }

  public function logout(Request $request) {
    Auth::logout();

    return response()->json(['message' => 'Logout Success']);
  }

  public function destroy($userId) {
    $user = User::find($userId);

    if($user) {
      $logMessage = 'El usuario '.$user->name.' ha eliminado al usuario id: '.$user->id;
      Logger::create(['message' => $logMessage]);

      $user->delete();

      return response()->json(['message' => 'Usuario ha sido eliminado']);
    } else {
      return response()->json($this->userNotFoundJson, 404);
    }

  }

  public function update(Request $request, $userId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $data = $request->only('name', 'email');
    $role = $request->input('role');
    $newPassword = $request->input('password');
    $newRole = $request->input('role');

    $validatorRules = [
      'name' => 'required',
      'email' => 'required|email|unique:users,email,'. $userId,
    ];
    $validator = Validator::make($data, $validatorRules, $this->validatorMessages);

    if($validator->fails()) {
      $errorsMessages = $validator->errors()->all();

      return response()->json(['message' => $errorsMessages[0]], 400);
    } else {
      $user = User::find($userId);

      if($user) {
        $user->update($data);

        if(strlen($newPassword) >= 4) {
          $user->password = bcrypt($newPassword);
          $user->save();
        }

        if($newRole == 1 and !$user->hasRole('admin')) {
          $user->roles()->attach(1);
        } else if($newRole == 2) {
          $user->roles()->detach(1);
        }

        $user->loadRole();
        $logMessage = 'El usuario '.$user->name.' ha actualizado al usuario id: '.$user->id;
        Logger::create(['message' => $logMessage]);

        return response()->json($user);

      } else {
        return response()->json($this->userNotFoundJson, 404);
      }

    }
  }
}