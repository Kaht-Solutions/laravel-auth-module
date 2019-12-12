<?php

namespace Modules\Auth\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Service\AuthService;

class CoachAuthController extends Controller
{

    public function login(Request $request)
    {
        $data = $request->all();
        $guard = 'api_coach';
        $type = 'mobile';
        $model = '\Modules\Veclu\Models\VecluCoach';

        $authService = new AuthService;
        $result = $authService->login($data, $model, $guard, $type);

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
