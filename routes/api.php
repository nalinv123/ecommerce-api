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

Route::group([
	'prefix' => 'admin'
], function () {
	Route::post('create', 'AdminController@create');
	Route::post('authenticate', 'AdminController@authenticate');

	Route::group([
		'middleware' => ['auth:api', 'admin']
	], function () {
		Route::get('logout', 'AdminController@logout');
		Route::get('getall', 'AdminController@getAll');
		Route::get('get/{id}', 'AdminController@get');
		Route::put('edit', 'AdminController@edit');
		Route::delete('delete/{id}', 'AdminController@remove');
	});
});

Route::group([
	'prefix' => 'customer'
], function () {
	Route::post('create', 'CustomerController@create');
	Route::post('authenticate', "CustomerController@authenticate");

	Route::group([
		'middleware' => ['auth:api', 'admin']
	], function () {
		Route::get('getall', 'CustomerController@getAll');
		Route::delete('delete/{id}', 'CustomerController@remove');
	});

	Route::group([
		'middleware' => ['auth:api']
	], function () {
		Route::get('logout', 'CustomerController@logout');
		Route::get('get/{id}', 'CustomerController@get');
		Route::put('edit', 'CustomerController@edit');
	});
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
