<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
//list all poes
Route::get('/posts/list','PostController@generated');
//list one after the user searches for it
Route::get('/posts/{id}','PostController@check');
//publish a new doc
Route::post('/posts/publish','PostController@store');
//search for a doc's POE
//Route::post('publish','PostController@check');
Route::apiResource('/posts','PostController');