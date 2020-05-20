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
            return serviceError($valid->errors()->all());
        }

        $user = $model::where([$type => $data[$type], 'password' => bcrypt($data['password'])])->first();
        $user = Auth::guard('web')
            ->attempt([$type => $data[$type], 'password' => $data['password']], false, false);

        if ($user) {
            return serviceOk($user);
        } else {
            return serviceError(trans('auth::messages.invalid_data'));
        }

        // if (Auth::guard($guard)->attempt([
        //     $type => $data[$type],
        //     'password' => $data['password']], $remember)) {
        //     return serviceOk(true);
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

        return serviceOk($user);

        // if (Auth::guard($guard)->attempt([
        //     $type => $data[$type],
        //     'password' => $data['password']], $remember)) {
        //     return serviceOk(true);
        // }

        // return serviceError(trans('auth::messages.invalid_data'));

    }

    public function send_activation_code($mobile, $model, $setting_model = null)
    {
        $mobile = fa_num_to_en($mobile);
        $user = $model::where('mobile', $mobile)->first();

        if ($setting_model) {
            $sms_period = $setting_model::get('sms_period');
        } else {
            $sms_period = 30;
        }

        if ($user && ((time() - $user->updated_at) > $sms_period)) {

            $user->activation_code = mt_rand(100, 999);
            $user->activation_code = 1111;

            // $client = new \GuzzleHttp\Client();
            // $res = $client->get('https://api.kavenegar.com/v1/736250476F2F305551614E5A4C5556505563563869413D3D/verify/lookup.json', ['query' => ['template' => 'phoenixVerify', 'receptor' => $mobile, 'token' => $user->activation_code]]);

            // if ($res->getStatusCode() != 200) {
            //     return ['is_successful' => false, 'message' => trans('auth::messages.sms_send_error')];
            // }

            $user->save();
            return serviceOk($user);
        } elseif (!$user) {
            return serviceError(trans('auth::messages.not_register'));

        } else {
            return serviceError(trans('auth::messages.wait_minutes'));
        }

    }

    public function check_activation_code($mobile, $activation_code, $model, $guard)
    {
        $activation_code = fa_num_to_en($activation_code);
        $mobile = fa_num_to_en($mobile);

        $user = $model::where(['mobile' => $mobile, 'activation_code' => $activation_code])->first();

        if ($user) {
            return serviceOk($user);
        } else {
            return serviceError(trans('auth::messages.not_register'));
        }

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
            ->json(serviceOk(true), 200)
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
