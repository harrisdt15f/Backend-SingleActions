<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 17:33:29
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 17:34:50
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryMethod;
use Illuminate\Http\JsonResponse;

class LotteriesMethodSwitchAction
{
    /**
     * 玩法开关
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        try {
            $pastData = LotteryMethod::find($inputDatas['id']);
            $pastData->status = $inputDatas['status'];
            $pastData->save();
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
