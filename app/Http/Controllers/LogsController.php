<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Member;
use App\Logger;
use Auth;

class LogsController extends Controller {
  private $notLoginJson = ['message' => 'No has iniciado session!'];
  private $needPermissionsJson = ['message' => 'No tiene los permisos necesarios'];

  public function index() {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $logs = Logger::orderBy('created_at', 'desc')->get();

    return response()->json($logs);
  }
}