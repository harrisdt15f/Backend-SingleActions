<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 20:40:39
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:01:32
 */
namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\FrontendUsersPrivacyFlow;
use App\Models\User\FrontendUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserHandleDeactivateAction
{
    protected $model;

    /**
     * @param  FrontendUser  $frontendUser
     */
    public function __construct(FrontendUser $frontendUser)
    {
        $this->model = $frontendUser;
    }

    /**
     * 用户冻结账号功能
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $userEloq = $this->model::find($inputDatas['user_id']);
        if ($userEloq !== null) {
            DB::beginTransaction();
            try {
                $userEloq->frozen_type = $inputDatas['frozen_type'];
                $userEloq->save();
                $userAdmitFlowLog = new FrontendUsersPrivacyFlow();
                $data = [
                    'admin_id' => $contll->partnerAdmin->id,
                    'admin_name' => $contll->partnerAdmin->name,
                    'user_id' => $userEloq->id,
                    'username' => $userEloq->username,
                    'comment' => $inputDatas['comment'],
                ];
                $userAdmitFlowLog->fill($data);
                $userAdmitFlowLog->save();
                DB::commit();
                return $contll->msgOut(true);
            } catch (Exception $e) {
                DB::rollBack();
                $errorObj = $e->getPrevious()->getPrevious();
                [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
                return $contll->msgOut(false, [], $sqlState, $msg);
            }
        }
    }
}
