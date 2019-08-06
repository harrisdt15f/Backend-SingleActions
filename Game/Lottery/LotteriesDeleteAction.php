<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LotteriesDeleteAction
{
    /**
     * 删除彩种
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        $lotteryEloq = LotteryList::find($inputDatas['id']);
        $pastIcon = $lotteryEloq->icon_path;
        $issueRuleEloq = $lotteryEloq->issueRule;
        $lotteryEloq->delete();
        if ($lotteryEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $lotteryEloq->errors()->messages());
        }
        foreach ($issueRuleEloq as $issueRuleItem) {
            $issueRuleItem->delete();
            if ($issueRuleItem->errors()->messages()) {
                DB::rollback();
                return $contll->msgOut(false, [], '400', $issueRuleItem->errors()->messages());
            }
        }
        DB::commit();
        $imageObj = new ImageArrange();
        $imageObj->deletePic(substr($pastIcon, 1));
        $lotteryEloq->lotteryInfoCache(); //更新首页lotteryInfo缓存
        FrontendLotteryRedirectBetList::updatePopularLotteriesCache(); //更新首页热门彩票缓存
        return $contll->msgOut(true);
    }
}
