<?php

namespace Modules\Auth\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\User;
use Redirect;
use Validator;

class UserAuthController extends Controller
{

    public function login_get()
    {
        return view('auth::login');
    }

    public function login_post(Request $request)
    {

        $data = $request->all();
        $valid = Validator::make($data, [
            'mobile' => 'required|regex:/[0-9]{9}/',
            'password' => 'required']);
        $remember = true;

        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        if (Auth::attempt([
            'mobile' => $data['mobile'],
            'password' => $data['password']], $remember)) {

            $user = Auth::user();
            $user->last_session = session()->getId();
            $user->save();
            // $user->createToken('laravel_token')->accessToken;

            return redirect('/vue_oauth');
        }

        return redirect('/auth/login')->withErrors(trans('auth::messages.invalid_data'))->withInput();

    }

    public function logout(Request $request)
    {

        Auth::logout();
        Auth::guard('web')->logout();

        \Session::flush();

        return redirect('/');
    }

    public function register_get()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('auth::register');

    }

    public function register_post(Request $request)
    {

        $data = $request->all();

        $valid = Validator::make($data, [
            'mobile' => 'required|regex:/[0-9]{9}/|unique:users',
            'password' => 'required|confirmed']);

        if ($valid->fails()) {
            return redirect('/auth/register')->withErrors($valid)->withInput();
        }

        $user = new User();
        $user->mobile = $data['mobile'];
        $user->password = bcrypt($data['password']);

        $user->save();

        if (Auth::attempt([
            'mobile' => $data['mobile'],
            'password' => $data['password']])) {
            return redirect()->intended('/');
        }

    }

}
