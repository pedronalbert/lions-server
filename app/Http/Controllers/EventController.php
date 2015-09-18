<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Member;
use Auth;


class EventController extends Controller {
  private $notLoginJson = ['message' => 'No has iniciado session!'];
  private $needPermissionsJson = ['message' => 'No tiene los permisos necesarios'];
  private $eventNotFoundJson = ['message' => 'Evento no encontrado'];

  public function index(Request $request) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('user')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $events = Event::orderBy('date', 'desc')->get();

    foreach ($events as $event) {
      $event->load('members');
      $event->loadResources();
    }

    return response()->json($events);
  }

  public function show(Request $request, $id) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('user')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $event = Event::find($id);

    if($event) {
      $event->load('members');
      $event->loadResources();
      
      return response()->json($event);
    } else {
      return response()->json($this->eventNotFoundJson, 404);
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

    $data = $request->only('title', 'description', 'location', 'sector', 'date');
    $data['active'] = 1;
    
    $event = Event::create($data);
    $event->load('members');
    $event->loadResources();

    return response()->json($event);
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

    $data = $request->only('title', 'description', 'location', 'sector', 'date');

    $event = Event::find($id);

    if($event) {
      $event->update($data);
      $event->load('members');
      $event->loadResources(); 

      return response()->json($event);
    } else {
      return response()->json($this->eventNotFoundJson, 404);
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

    $event = Event::find($id);

    if($event) {
      if($event->active == 1) {
        $event->finishEvent();
      }
      
      $event->delete();

      return response()->json(['message' => 'Evento ha sido eliminado exitosamente']);    
    } else {
      return response()->json($this->eventNotFoundJson, 404);
    }
  }

  public function addMember(Request $request, $eventId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $memberId = $request->input('member_id');
    $event = Event::find($eventId);

    if($event) {
      $member = $event->members()->where('member_id', $memberId)->count();

      if ($member > 0) {
        return response()->json(['message' => 'Miembro ya registrado'], 400);
      } else {
        $event->members()->attach($memberId);
        $member = Member::find($memberId);

        return response()->json($member);  
      }  
    } else {
      return response()->json($this->eventNotFoundJson, 404);
    }
  }

  public function removeMember(Request $request, $eventId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $memberId = $request->input('member_id');
    $event = Event::find($eventId);

    if($event) {
      $event->members()->detach($memberId);

      return response()->json(['message' => 'Miembro ha sido removido exitosamente']); 
    } else {
      return response()->json($this->eventNotFoundJson, 404);
    }
  }

  public function addResource(Request $request, $eventId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $data = $request->only('resource_id', 'amount', 'member_id');
    $data['event_id'] = $eventId;

    $event = Event::find($eventId);
    $resourceAdded = $event->addResource($data);

    if(!$resourceAdded) {
      $json = ['message' => $event->errorMessage];
      return response()->json($json, 400);
    } else {

      return response()->json($resourceAdded);
    }
  }

  public function removeResource(Request $request, $eventId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $data = $request->only('resource_id');
    $data['event_id'] = $eventId;
    $event = Event::find($eventId);
    
    if($event) {
      if($event->removeResource($data)) {

        return response()->json(['message' => 'Recurso removido exitosamente']);
      } else {
        return response()->json(['message' => 'Recurso no encontrado'], 404);
      }
    } else {
      return response()->json($this->eventNotFoundJson, 404);
    }
  }

  public function finishEvent(Request $request, $eventId) {
    if(!Auth::check()) {
      return response()->json($this->notLoginJson, 401);
    } else {
      $user = Auth::user();

      if(!$user->hasRole('admin')) {
        return response()->json($this->needPermissionsJson, 401);
      }
    }

    $event = Event::find($eventId);

    if($event) {
      if($event->active == 1) {
        $event->finishEvent();
      }      

      return response()->json(['message' => 'Evento marcado como finalizado']);
    } else {
      return response()->json($this->eventNotFoundJson, 404);
    }

  }
}
