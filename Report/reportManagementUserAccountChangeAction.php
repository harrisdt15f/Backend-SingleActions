<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 18:03:45
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 18:08:48
 */
namespace App\Http\SingleActions\Backend\Report;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsReport;
use Illuminate\Http\JsonResponse;

class reportManagementUserAccountChangeAction
{
    /**
     * 玩家帐变报表
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $accountChangeEloq = new FrontendUsersAccountsReport();
        $searchAbleFields = ['username', 'type_sign', 'is_for_agent'];
        $fixedJoin = 1;
        $withTable = 'changeType';
        $withSearchAbleFields = ['in_out', 'type'];
        $field = 'created_at';
        $type = 'desc';
        $datas = $contll->generateSearchQuery($accountChangeEloq, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields, $field, $type);
        foreach ($datas as $key => $report) {
            $data = $report->toArray();
            $reportArr = [
                'username' => $data['username'],
                'balance' => $data['balance'],
                'amount' => $data['amount'],
                'before_balance' => $data['before_balance'],
                'type_name' => $data['type_name'],
                'type_sign' => $data['type_sign'],
                'in_out' => $data['change_type']['in_out'],
                'created_at' => $data['created_at'],
            ];
            $datas[$key] = $reportArr;
        }
        return $contll->msgOut(true, $datas);
    }
}
