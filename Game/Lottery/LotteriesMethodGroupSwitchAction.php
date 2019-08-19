<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use App\Models\Game\Lottery\LotteryMethod;
use Exception;
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
        $methodGroupIds = LotteryMethod::where('lottery_id', $inputDatas['lottery_id'])
            ->where('method_group', $inputDatas['method_group'])
            ->pluck('id');
        if (empty($methodGroupIds)) {
            return $contll->msgOut(false, [], '101701');
        }
        try {
            $updateDate = ['status' => $inputDatas['status']];
            LotteryMethod::whereIn('id', $methodGroupIds)->update($updateDate);
            $contll->clearMethodCache(); //清理彩种玩法缓存
            LotteryList::lotteryInfoCache(); //更新首页lotteryInfo缓存
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
