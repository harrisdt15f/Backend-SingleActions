<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-26 18:24:44
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 18:30:04
 */
namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminUser;
use Illuminate\Http\JsonResponse;

class BackendAuthUpdateUserGroupAction
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
     * 修改管理员的归属组
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $targetUserEloq = $this->model::find($inputDatas['id']);
        if ($targetUserEloq !== null) {
            $targetUserEloq->group_id = $inputDatas['group_id'];
            try {
                $targetUserEloq->save();
                $result = $targetUserEloq->toArray();
                return $contll->msgOut(true, $result);
            } catch (Exception $e) {
                $errorObj = $e->getPrevious()->getPrevious();
                [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
                return $contll->msgOut(false, [], $sqlState, $msg);
            }
        }
    }
}
