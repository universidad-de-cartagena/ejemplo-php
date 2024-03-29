<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('notes', 'NotesController@index');
Route::get('notes/{uuid}', 'NotesController@show');
Route::post('notes', 'NotesController@create');
Route::delete('notes/{uuid}', 'NotesController@delete');
// Fallback when a note wants to be deleted without including an UUID
Route::delete('notes', 'NotesController@fallback');
