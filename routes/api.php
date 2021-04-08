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

Route::group(['namespace' => 'App\Http\Controllers\Api', 'middleware' => 'api'], function () {

	Route::post('/createorder', 'OrderController@createOrder')->name('createorder');
	Route::post('/processorder', 'OrderController@processOrder')->name('processorder');
	Route::post('/confirmorder', 'OrderController@confirmOrder')->name('confirmorder');
	
	Route::middleware('auth:api')->get('/user', function (Request $request) {
	    return $request->user();
	});
});


