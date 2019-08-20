<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
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
        $lotteryMethodEloq = LotteryMethod::find($inputDatas['id']);
        $lotteryMethodEloq->status = $inputDatas['status'];
        $lotteryMethodEloq->save();
        if ($lotteryMethodEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '', $lotteryMethodEloq->errors()->messages());
        }
        $contll->clearMethodCache(); //清理彩种玩法缓存
        LotteryList::lotteryInfoCache(); //更新首页lotteryInfo缓存
        return $contll->msgOut(true);
    }
}
