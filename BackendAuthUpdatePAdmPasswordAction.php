<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-26 19:06:15
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 20:13:34
 */
namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class BackendAuthUpdatePAdmPasswordAction
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
            try {
                $targetUserEloq->password = Hash::make($inputDatas['password']);
                $targetUserEloq->save();
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
