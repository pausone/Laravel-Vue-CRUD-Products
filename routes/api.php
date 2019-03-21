<?php

use Illuminate\Http\Request;
Use App\Product;

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

//Auth
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

Route::get('products', 'ProductController@index');

//Products can be viewed by all
Route::get('products', 'ProductController@index');
Route::get('products/{product}', 'ProductController@show');

//Login to change data
Route::group(['middleware' => 'auth:api'], function() {	
	Route::delete('products/{product}', 'ProductController@delete');
	Route::put('products/{product}', 'ProductController@update');
	Route::post('products', 'ProductController@store');
});


