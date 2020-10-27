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
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::resource('todos','TodoController',['only' => ['index','show']]);

//Route::resource('todos','TodoController',['except' => ['index','show']]);
Route::resource('todos','TodoController');

Route::resource('users','UserController');

Route::name('verify')->get('users/verify/{token}','UserController@verify');

//Route::post('users/{id}', 'UserController@update');
