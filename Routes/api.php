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
    'prefix' => 'authtest',
    'middleware' => ['auth:api_public', 'scope:vue_public_scope,mobile_public_scope'],
], function ($api) {

    /**One Step */
    // Route::post('/admin/login', 'API\OneStepExampleController@login');

    /**Two Step */
    // Route::post('/manager/send_activation_code', 'API\TwoStepExampleController@send_activation_code');
    // Route::post('/manager/check_activation_code', 'API\TwoStepExampleController@check_activation_code');

});
