<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Note;

Route::get('note', function() {
    return Note::all();
});

// Route::get('note/{uuid}', function($uuid) {
//     return Article::find($uuid);
// });

Route::post('note', function(Request $request) {
    return Article::create($request->all);
});

// Route::put('note/{id}', function(Request $request, $id) {
//     $article = Article::findOrFail($id);
//     $article->update($request->all());

//     return $article;
// });

// Route::delete('note/{id}', function($id) {
//     Article::find($id)->delete();

//     return 204;
// })