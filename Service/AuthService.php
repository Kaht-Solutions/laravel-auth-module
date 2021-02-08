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

        $user = Auth::guard('web')
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

            if ($key == 'birth_date') {
                $birth_date = explode('/', $data['birth_date']);
                $birth_date_g = \Morilog\Jalali\CalendarUtils::toGregorian($birth_date[0], $birth_date[1], $birth_date[2]);
                $birth_date_g = Carbon::createFromDate($birth_date_g[0], $birth_date_g[1], $birth_date_g[2]);
                $user->$key = $birth_date_g;
            }

            $isActivationCodeExist = Schema::connection("mysql")->hasColumn($model_obj->getTableName(), 'activation_code');
            if ($isActivationCodeExist) {
                $user->activation_code = 1111;
            }
        }

        $user->save();

        return serviceOk($user);
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

        $user = $model::where(['mobile' => $mobile])->first();

        if ($user && $user->activation_code == $activation_code) {
            return serviceOk($user);
        } elseif ($user) {
            return serviceError(trans('auth::messages.wrong_code'));
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
