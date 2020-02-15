<?php

namespace Modules\Auth\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Service\AuthService;

class TwoStepExampleController extends Controller
{

    public function send_activation_code(Request $request)
    {
        $data = $request->all();
        $guard = 'api_coach';
        $model = '\Modules\Biav\Models\BiavCoach';

        $authService = new AuthService;
        $result = $authService->send_activation_code($request->mobile, $model);

        if ($result['is_successful']) {

            return responseOk(trans('auth::messages.done'));

        } else {
            return responseError($result['message']);
        }

    }

    public function check_activation_code(Request $request)
    {
        $data = $request->all();
        $guard = 'api_coach';
        $model = '\Modules\Biav\Models\BiavCoach';

        $authService = new AuthService;
        $result = $authService->check_activation_code($request->mobile, $request->activation_code, $model, $guard);

        if ($result['is_successful']) {

            $client_id = auth()->user()->Token()->getAttribute('client_id');

            if ($client_id == 5) {
                return $authService->set_cookie_token($guard, $result['data']);
            } elseif ($client_id == 3) {
                return $authService->set_bearer_token($guard, $result['data']);
            }

        } else {
            return responseError($result['message']);
        }

    }

}
