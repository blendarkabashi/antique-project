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

Route::get('getItems', 'App\Http\Controllers\ApiController@getAllItems');
Route::get('getItem/{id}', 'App\Http\Controllers\ApiController@getItem');
Route::post('createItem', 'App\Http\Controllers\ApiController@createItem');
Route::post('updateBid/{id}', 'App\Http\Controllers\ApiController@updateBid');
Route::post('updateBidsHistory/{id}', 'App\Http\Controllers\ApiController@updateBidsHistory');
Route::put('updateItem/{id}', 'App\Http\Controllers\ApiController@updateItem');
Route::delete('deleteItem/{id}','App\Http\Controllers\ApiController@deleteItem');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
