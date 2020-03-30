<?php

//use Illuminate\Support\Facades\Route;

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
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api' //auth is api
    ], function () {
        Route::get('logout', 'AuthController@logout');
        Route::post('update','AuthController@updateProfile');
        Route::post('changepwd','AuthController@changePassword');
    });
});

Route::get('all-posts','APIController@getPost');
Route::post('users-posts','APIController@getUserPost');
Route::get('all-vehicles','APIController@getVehicleInfo');
Route::post('create-post','APIController@storePost');
Route::post('update-post','APIController@updatePost');
Route::post('delete-post','APIController@deletePost');
Route::post('delete-vehicle','APIController@deleteVehicleInfo');
Route::post('sold-request','APIController@makeSoldOut');
