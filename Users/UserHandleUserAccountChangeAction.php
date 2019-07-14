<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 21:10:48
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 21:12:35
 */
namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\FrontendUser;
use App\Models\User\Fund\FrontendUsersAccountsReport;
use Illuminate\Http\JsonResponse;

class UserHandleUserAccountChangeAction
{
    /**
     * 用户帐变记录
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $datas = FrontendUsersAccountsReport::select('username', 'type_name', 'type_sign', 'amount', 'before_balance', 'balance', 'created_at')->with('changeType')->where('user_id', $inputDatas['user_id'])->whereBetween('created_at', [$inputDatas['start_time'], $inputDatas['end_time']])->get()->toArray();
        foreach ($datas as $key => $report) {
            $datas[$key]['in_out'] = $report['change_type']['in_out'];
            unset($datas[$key]['type_sign'], $datas[$key]['change_type']);
        }
        return $contll->msgOut(true, $datas);
    }
}
