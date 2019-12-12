<?php

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
    'prefix' => 'auth',
    'middleware' => ['auth:api_public', 'scope:vue_public_scope,mobile_public_scope'],
], function ($api) {

    Route::post('/admin/login', 'API\AdminAuthController@login');

    Route::get('/configs', 'API\AdminAuthController@configs');

    Route::post('/manager/login', 'API\ManagerAuthController@login');
    Route::post('/manager/register', 'API\ManagerAuthController@register');

    Route::post('/coach/login', 'API\CoachAuthController@login');
    Route::post('/coach/register', 'API\CoachAuthController@register');

    Route::post('/player/login', 'API\PlayerAuthController@login');
    Route::post('/player/register', 'API\PlayerAuthController@register');

});

Route::group(['middleware' => ['auth:api_public', 'scope:vue_public_scope,mobile_public_scope']], function () {
    Route::any('/test', 'API\UserAuthController@login');

});
