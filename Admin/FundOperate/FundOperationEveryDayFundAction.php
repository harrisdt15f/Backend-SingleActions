<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 14:16:05
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:16:08
 */
namespace App\Http\SingleActions\Backend\Admin\FundOperate;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\SystemConfiguration;
use Exception;
use Illuminate\Http\JsonResponse;

class FundOperationEveryDayFundAction
{
    /**
     * 设置每日的管理员人工充值额度
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $sysConfiguresEloq = SystemConfiguration::where('sign', 'admin_recharge_daily_limit')->first();
        if ($sysConfiguresEloq === null) {
            return $contll->msgOut(false, [], '101301');
        }
        try {
            $editData = ['value' => $inputDatas['fund']];
            $sysConfiguresEloq->fill($editData);
            $sysConfiguresEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
