<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-27 15:55:15
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 15:59:36
 */
namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class BackendAuthLogoutAction
{
    use AuthenticatesUsers;
    /**
     * Logout user (Revoke the token)
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $request): JsonResponse
    {
        $throtleKey = Str::lower($contll->username() . '|' . $request->ip());
        $request->session()->invalidate();
        $this->limiter()->clear($throtleKey);
        $contll->currentAuth->logout();
        $contll->currentAuth->invalidate();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
