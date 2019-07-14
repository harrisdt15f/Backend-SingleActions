<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryIssueRule;
use App\Models\Game\Lottery\LotteryList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LotteriesAddAction
{
    /**
     * 添加彩种
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        $lotteryEloq = new LotteryList();
        $lotteryEloq->fill($inputDatas['lottery']);
        $lotteryEloq->save();
        if ($lotteryEloq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $lotteryEloq->errors()->messages());
        }
        $issueRuleELoq = new LotteryIssueRule();
        $issueRuleELoq->fill($inputDatas['issue_rule']);
        $issueRuleELoq->save();
        if ($issueRuleELoq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $issueRuleELoq->errors()->messages());
        }
        DB::commit();
        $lotteryEloq->lotteryInfoCache(); //更新首页lotteryInfo缓存
        return $contll->msgOut(true);
    }
}
