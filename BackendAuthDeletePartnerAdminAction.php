<?php

namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class BackendAuthDeletePartnerAdminAction
{
    protected $model;

    /**
     * @param  BackendAdminUser  $backendAdminUser
     */
    public function __construct(BackendAdminUser $backendAdminUser)
    {
        $this->model = $backendAdminUser;
    }

    /**
     * 删除管理员
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $targetUserEloq = $this->model::where([
            ['id', '=', $inputDatas['id']],
            ['name', '=', $inputDatas['name']],
        ])->first();
        if ($targetUserEloq !== null) {
            if ($targetUserEloq->remember_token !== null) {
                try {
                    JWTAuth::setToken($targetUserEloq->remember_token);
                    JWTAuth::invalidate();
                } catch (JWTException $e) {
                    Log::info($e->getMessage());
                }
            }
            try {
                $targetUserEloq->delete();
                return $contll->msgOut(true);
            } catch (Exception $e) {
                $errorObj = $e->getPrevious()->getPrevious();
                [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
                return $contll->msgOut(false, [], $sqlState, $msg);
            }
        } else {
            return $contll->msgOut(false, [], '100004');
        }
    }
}
