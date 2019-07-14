<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 21:14:32
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 21:16:44
 */
namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\UsersRechargeHistorie;
use Illuminate\Http\JsonResponse;

class UserHandleUserRechargeHistoryAction
{
    /**
     * 用户充值记录
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $datas = UsersRechargeHistorie::select('user_name', 'amount', 'deposit_mode', 'status', 'created_at')->where('user_id', $inputDatas['user_id'])->whereBetween('created_at', [$inputDatas['start_time'], $inputDatas['end_time']])->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
