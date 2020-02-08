<?php

namespace Modules\Auth\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Service\AuthService;

class OneStepExampleController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->all();
        $guard = 'api_admin';
        $type = 'mobile';
        $model = '\Modules\Auth\Models\User';

        $authService = new AuthService;
        $result = $authService->login($data, $model, $guard, $type);

        if ($result['is_successful']) {
            $user = $model::where($type, $data[$type])->first();

            $client_id = auth()->user()->Token()->getAttribute('client_id');

            if ($client_id == 5) {
                return $authService->set_cookie_token($guard, $user);
            } elseif ($client_id == 3) {
                return $authService->set_bearer_token($guard, $user);
            }

        } else {
            return responseError($result['message']);
        }

    }

}
