<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Logger;
use Auth;
use App\Event;
use App\Member;
use App\Resource;
use App\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Event::created(function ($event) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha registrado el evento id: '.$event->id;
          
          Logger::create(['message' => $message]);
        });

        Event::updating(function ($event) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha actualizado el evento id: '.$event->id;
          
          Logger::create(['message' => $message]);
        });

        Event::deleting(function ($event) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha eliminado el evento id: '.$event->id;
          
          Logger::create(['message' => $message]);
        });

        Member::created(function ($member) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha registrado al miembro id: '.$member->id;
          
          Logger::create(['message' => $message]);
        });

        Member::updating(function ($member) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha actualizado al miembro id: '.$member->id;
          
          Logger::create(['message' => $message]);
        });

        Member::deleting(function ($member) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha eliminado al miembro id: '.$member->id;
          
          Logger::create(['message' => $message]);
        });

        Resource::created(function ($resource) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha registrado el recurso id: '.$resource->id;
          
          Logger::create(['message' => $message]);
        });

        Resource::updating(function ($resource) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha actualizado el recurso id: '.$resource->id;
          
          Logger::create(['message' => $message]);
        });

        Resource::deleting(function ($resource) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha eliminado el recurso id: '.$resource->id;
          
          Logger::create(['message' => $message]);
        });

        User::created(function ($user) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha registrado al usuario id: '.$user->id;
          
          Logger::create(['message' => $message]);
        });

        User::updating(function ($user) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha actualizado al usuario id: '.$user->id;
          
          Logger::create(['message' => $message]);
        });

        User::deleting(function ($user) {
          $user = Auth::user();
          $message = 'El usuario '.$user->name.' ha eliminado al usuario id: '.$user->id;
          
          Logger::create(['message' => $message]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
