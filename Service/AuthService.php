<?php

namespace Modules\Auth\Service;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Validator;

class AuthService
{

    public function login($data, $model = '\Modules\Auth\Models\User', $guard = 'web', $type = 'mobile', $remember = true)
    {
        $model_obj = new $model;

        $valid = Validator::make($data, [
            $type => 'required|exists:' . $model_obj->getTableName() . ',' . $type,
            'password' => 'required'
        ]);
        if ($valid->fails()) {
            return serviceError($valid->errors()->all());
        }

        $user = Auth::guard($guard)
            ->attempt([$type => $data[$type], 'password' => $data['password']], false, false);

        if ($user) {
            return serviceOk($user);
        } else {
            return serviceError(trans('auth::messages.invalid_data'));
        }
    }

    public function register($data, $model = '\Modules\Auth\Models\User', $guard = 'web', $type = 'mobile', $remember = true)
    {

        $model_obj = new $model;

        $valid = Validator::make($data, [
            $type => 'required|unique:' . $model_obj->getTableName(),
        ]);

        if ($valid->fails()) {
            return serviceError($valid->errors()->all());
        }

        $user = new $model;
        foreach ($data as $key => $value) {
            $user->$key = $value;

            if ($key == 'password') {
                $user->$key = bcrypt($value);
            }

            $is_activation_code_column_exist = Schema::connection("mysql")->hasColumn($model_obj->getTableName(), 'activation_code');
            if ($is_activation_code_column_exist) {
                if (env("APP_ENV") == "local") {
                    $user->activation_code = 1111;
                } else {
                    $user->activation_code = mt_rand(1000, 9999);
                }
            }
        }

        $user->save();

        return serviceOk($user);
    }

    public function sendActivationCode($mobile, $model, $setting_model = null, $sms_period = 30)
    {
        $mobile = ConvertPersianAndArabicToEnglishNumbers($mobile);
        $user = $model::where('mobile', $mobile)->first();

        if ($setting_model) {
            $sms_period = $setting_model::get('sms_period');
        }

        if ($user && ((time() - $user->updated_at) > $sms_period) || $sms_period == 0) {

            if (env("APP_ENV") == "local") {
                $user->activation_code = 1111;
            } else {
                $user->activation_code = mt_rand(1000, 9999);
            }

            $user->save();
            return serviceOk($user);
        } elseif (!$user) {
            return serviceError(trans('auth::messages.not_register'));
        } else {
            return serviceError(trans('auth::messages.wait_minutes'));
        }
    }

    public function checkActivationCode($mobile, $activation_code, $model, $guard)
    {
        $activation_code = ConvertPersianAndArabicToEnglishNumbers($activation_code);
        $mobile = ConvertPersianAndArabicToEnglishNumbers($mobile);

        $user = $model::where(['mobile' => $mobile])->first();

        if ($user && $user->activation_code == $activation_code) {
            return serviceOk($user);
        } elseif ($user) {
            return serviceError(trans('auth::messages.wrong_code'));
        } else {
            return serviceError(trans('auth::messages.not_register'));
        }
    }

    public function setSession($guard, $user)
    {
        Auth::guard($guard)->loginUsingId($user->id);
        return true;
    }

    public function setCookieToken($guard, $user)
    {
        $token = $user->createToken($guard . '_' . $user->id . '_cookie', [$guard])->accessToken;

        $cookie = $this->getCookieDetails($token);

        return response()
            ->json(serviceOk(true), 200)
            ->cookie(
                $cookie['name'],
                $cookie['value'],
                $cookie['minutes'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly'],
                $cookie['samesite']
            )
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

    public function setBearerToken($guard, $user)
    {
        $token = $user->createToken($guard . '_' . $user->id . '_bearer', [$guard]);
        return $token->accessToken;
    }

    private function getCookieDetails($token, $test_mode = true)
    {
        if (env("APP_ENV") == "local") {
            $secure = null; // for local
        } else {
            $secure = true; // for production
        }
        return [
            'name' => '_token',
            'value' => $token,
            'minutes' => 1440,
            'path' => null,
            'domain' => null,
            'secure' => $secure,
            'httponly' => true,
            'samesite' => "none",
        ];
    }
}
