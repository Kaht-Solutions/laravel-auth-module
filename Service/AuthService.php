<?php

namespace Modules\Auth\Service;

use Auth;
use Validator;

class AuthService
{

    public function login($data, $model = '\Modules\Auth\Models\User', $guard = 'web', $type = 'mobile', $remember = true)
    {
        $model_obj = new $model;

        $valid = Validator::make($data, [
            $type => 'required|exists:' . $model_obj->getTableName() . ',' . $type,
            'password' => 'required']);

        if ($valid->fails()) {
            return responseError($valid->errors()->all());
        }

        $user = $model::where([$type => $data[$type], 'password' => bcrypt($data['password'])])->first();
        $user = Auth::guard('web')
            ->attempt([$type => $data[$type], 'password' => $data['password']], false, false);

        if ($user) {
            return responseOk($user);
        } else {
            return responseError(trans('auth::messages.invalid_data'));
        }

        // if (Auth::guard($guard)->attempt([
        //     $type => $data[$type],
        //     'password' => $data['password']], $remember)) {
        //     return responseOk(true);
        // }

        // return ['is_successful' => false, 'message' => trans('auth::messages.not_register')];

    }

    public function register($data, $model = '\Modules\Auth\Models\User', $guard = 'web', $type = 'mobile', $remember = true)
    {

        $model_obj = new $model;

        $valid = Validator::make($data, [
            $type => 'required|unique:' . $model_obj->getTableName(),
        ]);

        if ($valid->fails()) {
            return $valid->errors()->all();
        }

        $user = new $model;
        foreach ($data as $key => $value) {
            $user->$key = $value;

            if ($key == 'password') {
                $user->$key = bcrypt($value);
            }

        }

        $user->save();

        return responseOk($user);

        // if (Auth::guard($guard)->attempt([
        //     $type => $data[$type],
        //     'password' => $data['password']], $remember)) {
        //     return responseOk(true);
        // }

        // return responseError(trans('auth::messages.invalid_data'));

    }

    public function send_activation_code($mobile, $model)
    {
        $mobile = fa_num_to_en($mobile);
        $user = $model::where('mobile', $mobile)->first();

        if ($user && ((time() - $user->updated_at) > Setting::get('sms_period'))) {

            $user->activation_sms = mt_rand(100, 999);
            $user->activation_sms = 1111;

            // $client = new \GuzzleHttp\Client();
            // $res = $client->get('https://api.kavenegar.com/v1/736250476F2F305551614E5A4C5556505563563869413D3D/verify/lookup.json', ['query' => ['template' => 'phoenixVerify', 'receptor' => $mobile, 'token' => $user->activation_sms]]);

            // if ($res->getStatusCode() != 200) {
            //     return ['is_successful' => false, 'message' => trans('auth::messages.sms_send_error')];
            // }

            $user->save();
            return responseOk($user);
        } elseif (!$user) {
            return responseError(trans('auth::messages.not_register'));

        } else {
            return responseError(trans('auth::messages.wait_minutes'));
        }

    }

    public function check_activation_code($mobile, $activation_code, $model, $guard)
    {
        $activation_sms = fa_num_to_en($activation_code);
        $mobile = fa_num_to_en($mobile);

        $$user = $model::where(['mobile' => $mobile, 'activation_sms' => $activation_sms])->first();

        if ($$user) {
            return responseOk($user);
        } else {
            return responseError(trans('auth::messages.not_register'));
        }

        // if ($user) {
        //     if ($request->filled('is_cookie') && $request->is_cookie) {
        //         Auth::guard($guard)->loginUsingId($user->id);
        //         return [
        //             'is_successful' => true,
        //             'data' => [true],
        //         ];
        //     } else {
        //         return [
        //             'is_successful' => true,
        //             'data' => ['token' => $user->createToken('user_api_token_' . $user->id, ['user_panel'])->accessToken],
        //         ];
        //     }
        // }

        // return responseError(false)
    }

    public function set_session($guard, $user)
    {
        Auth::guard($guard)->loginUsingId($user->id);
        return true;
    }

    public function set_cookie_token($guard, $user)
    {
        $token = $user->createToken($guard . '_' . $user->id . '_cookie', [$guard])->accessToken;

        $cookie = $this->getCookieDetails($token);

        return response()
            ->json(responseOk(true), 200)
            ->cookie($cookie['name'],
                $cookie['value'],
                $cookie['minutes'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly'],
                $cookie['samesite'])
            ->cookie(
                'expire_session',
                '',
                $cookie['minutes'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                false,
                $cookie['samesite']
            );

    }

    public function set_bearer_token($guard, $user)
    {
        $token = $user->createToken($guard . '_' . $user->id . '_bearer', [$guard])->accessToken;
        return $token;
    }

    private function getCookieDetails($token)
    {
        return [
            'name' => '_token',
            'value' => $token,
            'minutes' => 1440,
            'path' => null,
            'domain' => null,
            // 'secure' => true, // for production
            'secure' => null, // for localhost
            'httponly' => true,
            'samesite' => "none",
        ];
    }

}
