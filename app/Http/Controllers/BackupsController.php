<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\BackupsMiddleware;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Backup;
class BackupsController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
  public function index() {
      $backups = Backup::all();

      return response()->json($backups);
  }


  public function store() {
    $dir = substr(__DIR__, 0, 24).'database/backups/';

    $newBackup = Backup::create();

    $command = 'mysqldump -uroot lions > '.$dir.$newBackup->getDateTimeString().'.sql';

    exec($command);

    return response()->json(['message' => 'Backup success!']);
  }
}
