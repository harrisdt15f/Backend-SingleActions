<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 14:22:02
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:16:14
 */
namespace App\Http\SingleActions\Backend\Admin\FundOperate;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\Fund\BackendAdminRechargehumanLog;
use Illuminate\Http\JsonResponse;

class FundOperationFundChangeLogAction
{
    /**
     * 查看管理员人工充值额度记录
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $datas = BackendAdminRechargehumanLog::where('admin_id', $inputDatas['admin_id'])->whereBetween('created_at', [$inputDatas['start_time'], $inputDatas['end_time']])->get()->toArray();
        return $contll->msgout(true, $datas);
    }
}
