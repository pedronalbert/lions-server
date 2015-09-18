<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::resource('member', 'MemberController');
Route::resource('resource', 'ResourceController');
Route::resource('event', 'EventController');
Route::resource('user', 'UserController');
Route::resource('backups', 'BackupsController');

Route::post('event/{eventId}/add_member', 'EventController@addMember');
Route::post('event/{eventId}/remove_member', 'EventController@removeMember');
Route::post('event/{eventId}/add_resource', 'EventController@addResource');
Route::post('event/{eventId}/remove_resource', 'EventController@removeResource');
Route::post('event/{eventId}/finish_event', 'EventController@finishEvent');
Route::post('user/{id}/add_role', 'UserController@addRole');
Route::post('user/{id}/remove_role', 'UserController@removeRole');
Route::post('user/login', 'UserController@login');
Route::post('user/logout', 'UserController@logout');
Route::post('user/logged', 'UserController@logged');
Route::get('logs', 'LogsController@index');