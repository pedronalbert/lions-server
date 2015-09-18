<?php

namespace App\Http\Middleware;

use Closure;
use DateTime;
use App\Backup;

class BackupsMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next) {
    $lastBackup = Backup::orderBy('created_at', 'desc')->first();

    if(!$lastBackup) {
      $this->createNewBackup();
    } else {
      $lastBackupDate = new DateTime($lastBackup->created_at);
      $nowDate = new DateTime();

      $interval = $lastBackupDate->diff($nowDate);

      if($interval->h >= 24) {
        $this->createNewBackup;
      }
    }

    return $next($request);
  }

  public function createNewBackup() {
    $dir = substr(__DIR__, 0, 24).'database/backups/';

    $newBackup = Backup::create();

    $command = '/opt/lampp/bin/mysqldump -uroot lions > '.$dir.$newBackup->getDateTimeString().'.sql';

    exec($command);
  }
}
