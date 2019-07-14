<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 17:26:18
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 17:28:10
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryMethod;
use Illuminate\Http\JsonResponse;

class LotteriesMethodGroupSwitchAction
{
    /**
     * 玩法组开关
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $methodGroupIds = LotteryMethod::where('lottery_id', $inputDatas['lottery_id'])->where('method_group', $inputDatas['method_group'])->pluck('id');
        if (empty($methodGroupIds)) {
            return $contll->msgOut(false, [], '101701');
        }
        try {
            $updateDate = ['status' => $inputDatas['status']];
            LotteryMethod::whereIn('id', $methodGroupIds)->update($updateDate);
            //清理彩种玩法缓存
            $contll->clearMethodCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
