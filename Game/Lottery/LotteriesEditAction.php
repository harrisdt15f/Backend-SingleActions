<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryIssueRule;
use App\Models\Game\Lottery\LotteryList;
use Exception;
use Illuminate\Http\JsonResponse;
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
        $isExistCnName = LotteryList::where([
            ['id', '!=', $inputDatas['lottery']['id']],
            ['cn_name', $inputDatas['lottery']['cn_name']],
        ])->exists();
        if ($isExistCnName === true) {
            return $contll->msgOut(false, [], '101705');
        }
        $isExistEnName = LotteryList::where([
            ['id', '!=', $inputDatas['lottery']['id']],
            ['en_name', $inputDatas['lottery']['en_name']],
        ])->exists();
        if ($isExistEnName === true) {
            return $contll->msgOut(false, [], '101706');
        }
        DB::beginTransaction();
        $lotteryEloq = LotteryList::find($inputDatas['lottery']['id']);
        $issueRuleEloq = $lotteryEloq->issueRule;
        $contll->editAssignment($lotteryEloq, $inputDatas['lottery']);
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
        $lotteryEloq->lotteryInfoCache(); //更新首页lotteryInfo缓存
        return $contll->msgOut(true);
    }
}
