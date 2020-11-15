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
Route::post('auctionWonNotification', 'App\Http\Controllers\ApiController@auctionWonNotification');
Route::post('bidMadeNotification', 'App\Http\Controllers\ApiController@bidMadeNotification');
Route::get('getBids', 'App\Http\Controllers\ApiController@getAllBids');
Route::get('getItems', 'App\Http\Controllers\ApiController@getAllItems');
Route::get('getItem/{id}', 'App\Http\Controllers\ApiController@getItem');
Route::get('getBidsByUser/{user_id}', 'App\Http\Controllers\ApiController@getBidsByUser');
Route::get('getBidsByItem/{item_id}', 'App\Http\Controllers\ApiController@getBidsByItem');
Route::get('getUser/{id}', 'App\Http\Controllers\ApiController@getUser');
Route::get('getUsers', 'App\Http\Controllers\ApiController@getAllUsers');
Route::post('login', 'App\Http\Controllers\ApiController@login');
Route::post('logout', 'App\Http\Controllers\ApiController@logout');
Route::post('register', 'App\Http\Controllers\ApiController@register');
Route::post('createItem', 'App\Http\Controllers\ApiController@createItem');
Route::post('createBid', 'App\Http\Controllers\ApiController@createBid');
Route::post('makeBid/{id}', 'App\Http\Controllers\ApiController@makeBid');
Route::post('updateBidsHistory/{id}', 'App\Http\Controllers\ApiController@updateBidsHistory');
Route::put('updateItem/{id}', 'App\Http\Controllers\ApiController@updateItem');
Route::delete('deleteItem/{id}','App\Http\Controllers\ApiController@deleteItem');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
