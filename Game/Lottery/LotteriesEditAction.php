<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\CacheRelated;
use App\Lib\Common\ImageArrange;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class LotteriesEditAction
{
    /**
     * 编辑彩种
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        $lotteryEloq = LotteryList::find($inputDatas['lottery']['id']);
        $lotteryData = $inputDatas['lottery'];
        if (isset($lotteryData['icon_name'])) {
            $iconName = Arr::pull($lotteryData, 'icon_name');
            $pastIcon = $lotteryEloq->icon_path;
            $lotteryData['icon_path'] = '/' . $lotteryData['icon_path'];
        }
        $issueRuleEloq = $lotteryEloq->issueRule;
        $contll->editAssignment($lotteryEloq, $lotteryData);
        $lotteryEloq->save();
        if ($lotteryEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $lotteryEloq->errors()->messages());
        }
        $contll->editAssignment($issueRuleEloq, $inputDatas['issue_rule']);
        $issueRuleEloq->save();
        if ($issueRuleEloq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $issueRuleEloq->errors()->messages());
        }
        DB::commit();
        if (isset($iconName)) {
            CacheRelated::deleteCachePic($iconName); //从定时清理的缓存图片中移除上传成功的图片
        }
        if (isset($pastIcon)) {
            ImageArrange::deletePic(substr($pastIcon, 1));
        }
        $lotteryEloq->lotteryInfoCache(); //更新首页lotteryInfo缓存
        return $contll->msgOut(true);
    }
}
