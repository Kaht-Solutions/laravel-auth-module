<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::group(['middleware' => ['web', 'clearcache', 'guest'], 'prefix' => 'auth'], function () {

    Route::get('/', 'UserAuthController@login_get');
    Route::get('/login', 'UserAuthController@login_get')->name('login');
    Route::post('/login', 'UserAuthController@login_post');
    Route::get('/register', 'UserAuthController@register_get');
    Route::post('/register', 'UserAuthController@register_post');

    // Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    // Route::post('/password/reset', 'ResetPasswordController@reset');
    // Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    // Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');

});

Route::group(['middleware' => ['web']], function () {

    Route::get('/auth/logout', 'UserAuthController@logout');

});

Route::get('/redirect', function (Request $request) {

    // $request->session()->put('state', $state = Str::random(40));

    $query = http_build_query([
        'client_id' => '1',
        'redirect_uri' => env('APP_URL').'/final',
        'response_type' => 'token',
        'scope' => 'vue_public_scope',
        // 'state' => $state,
    ]);

    return redirect(env('APP_URL').'/oauth/authorize?' . $query);
});

Route::get('final', function (Request $request) {
    return 'check_url';
});
