<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 10:06:28
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:15:54
 */
namespace App\Http\SingleActions\Backend\Admin\FundOperate;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminUser;
use App\Models\Admin\Fund\BackendAdminRechargePermitGroup;
use App\Models\Admin\SystemConfiguration;
use Illuminate\Http\JsonResponse;

class FundOperationAdminDetailAction
{
    /**
     * 额度管理列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $group = BackendAdminRechargePermitGroup::select('group_id')->pluck('group_id')->toArray();
        $this->inputs['extra_where']['method'] = 'whereIn';
        $this->inputs['extra_where']['key'] = 'group_id';
        $this->inputs['extra_where']['value'] = $group;
        $eloqM = new BackendAdminUser();
        $fixedJoin = 1; //number of joining tables
        $withTable = 'operateAmount';
        $searchAbleFields = ['name', 'group_id'];
        $withSearchAbleFields = ['fund'];
        $orderFields = 'id';
        $orderFlow = 'asc';
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields, $orderFields, $orderFlow);
        $sysConfiguresEloq = SystemConfiguration::where('sign', 'admin_recharge_daily_limit')->first();
        $finalData['admin_user'] = $data;
        $finalData['dailyFundLimit'] = $sysConfiguresEloq['value'];
        return $contll->msgOut(true, $finalData);
    }
}
