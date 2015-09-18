<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\BackupsMiddleware;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Backup;
use Auth;

class BackupsController extends Controller
{
  private $notLoginJson = ['message' => 'No has iniciado session!'];
  private $needPermissionsJson = ['message' => 'No tiene los permisos necesarios'];
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index() {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $backups = Backup::orderBy('created_at', 'desc')->get();

    foreach ($backups as $backup) {
      $backup->setPrettyName();
    }

    return response()->json($backups);
  }


  public function store() {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $dir = substr(__DIR__, 0, 24).'database/backups/';

    $newBackup = Backup::create();

    $command = '/opt/lampp/bin/mysqldump -uroot lions > '.$dir.$newBackup->getDateTimeString().'.sql';

    exec($command);

    return response()->json(['message' => 'Backup success!']);
  }
}
