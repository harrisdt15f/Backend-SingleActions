<?php

namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class BackendAuthSelfResetPasswordAction
{
    /**
     * change partner user Password
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {

        if (!Hash::check($inputDatas['old_password'], $contll->partnerAdmin->password)) {
            return $contll->msgOut(false, [], '100003');
        } else {
            $token = $contll->refresh();
            $contll->partnerAdmin->password = Hash::make($inputDatas['password']);
            $contll->partnerAdmin->remember_token = $token;
            try {
                $contll->partnerAdmin->save();
                $expireInMinute = $contll->currentAuth->factory()->getTTL();
                $expireAt = Carbon::now()->addMinutes($expireInMinute)->format('Y-m-d H:i:s');
                $data = [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_at' => $expireAt,
                ];
                return $contll->msgOut(true, $data);
            } catch (Exception $e) {
                $errorObj = $e->getPrevious()->getPrevious();
                [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
                return $contll->msgOut(false, [], $sqlState, $msg);
            }
        }
    }
}
