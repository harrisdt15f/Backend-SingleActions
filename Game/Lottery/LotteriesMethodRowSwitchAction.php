<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 17:30:48
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:42:59
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryMethod;
use Illuminate\Http\JsonResponse;

class LotteriesMethodRowSwitchAction
{
    /**
     * 玩法行开关
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $methodGroupIds = LotteryMethod::where([
            ['lottery_id', $inputDatas['lottery_id']],
            ['method_group', $inputDatas['method_group']],
            ['method_row', $inputDatas['method_row']],
        ])->pluck('id');
        if (empty($methodGroupIds)) {
            return $contll->msgOut(false, [], '101702');
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
