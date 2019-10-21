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

//Route::post('login', 'Api\UserController@login');
//Route::post('register', 'Api\UserController@register');
Route::post('admin_register', 'AuthController@adminRegister');
Route::post('admin_login', 'AuthController@adminLogin');
//Route::group(['middleware' => 'auth:api'], function() {
Route::middleware(['auth:api', 'scopes:admin'])->group( function() {
    Route::post('coordinator_register', 'AuthController@coordinatorRegister');
});

Route::post('login', 'AuthController@login');
Route::post('register', 'AuthController@register');
Route::post('check_token', 'AuthController@checkToken');

/*
Route::group(['middleware' => 'auth:api'], function() {
    Route::post('details', 'Api\UserController@details');
//});

Route::get('/api/users', 'UserController@coordinator')
	->middleware(['auth:api', 'scopes:coordinator']);
Route::get('/api/users', 'UserController@tutor')
	->middleware(['api:auth', 'scopes:tutor']);
//Route::group(['namespace' => 'Auth'], function() {
	Route::post('login', 'Auth\ApiLoginController@login');
//});
});
*/
