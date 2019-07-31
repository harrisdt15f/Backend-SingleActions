<?php

namespace App\Http\SingleActions\Backend\Report;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\UsersRechargeHistorie;
use Illuminate\Http\JsonResponse;

class reportManagementUserRechargeHistoryAction
{
    /**
     * 玩家充值报表
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $rechargeHistoryEloq = new UsersRechargeHistorie();
        $searchAbleFields = ['user_name', 'company_order_num', 'deposit_mode', 'status'];
        $field = 'updated_at';
        $type = 'desc';
        $datas = $contll->generateSearchQuery($rechargeHistoryEloq, $searchAbleFields, 0, null, null, $field, $type);
        return $contll->msgOut(true, $datas);
    }
}
