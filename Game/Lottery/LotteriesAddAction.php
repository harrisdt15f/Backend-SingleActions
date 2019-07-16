<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryIssueRule;
use App\Models\Game\Lottery\LotteryList;
use App\Models\Game\Lottery\LotteryMethod;
use App\Models\Game\Lottery\LotteryMethodsExample;
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
        $insertStatus = $this->insertLotteryMethods($lotteryEloq); //插入彩种玩法
        if ($insertStatus['success'] === false) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $insertStatus['message']);
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

    /**
     * 插入彩种的玩法
     * @param  $lotteryEloq
     * @return array
     */
    public function insertLotteryMethods($lotteryEloq): array
    {
        $examplesEloq = LotteryMethodsExample::where('series_id', $lotteryEloq->series_id)->get();
        foreach ($examplesEloq as $exampleEloq) {
            $data = [
                'series_id' => $lotteryEloq->series_id,
                'lottery_name' => $lotteryEloq->cn_name,
                'lottery_id' => $lotteryEloq->en_name,
                'method_id' => $exampleEloq->method_id,
                'method_name' => $exampleEloq->method_name,
                'method_group' => $exampleEloq->method_group,
                'method_row' => $exampleEloq->method_row,
                'group_sort' => $exampleEloq->group_sort,
                'row_sort' => $exampleEloq->row_sort,
                'method_sort' => $exampleEloq->method_sort,
                'show' => $exampleEloq->show,
                'status' => $exampleEloq->status,
                'total' => $exampleEloq->total,
            ];
            $lotteryMethodEloq = new LotteryMethod();
            $lotteryMethodEloq->fill($data);
            $lotteryMethodEloq->save();
            if ($lotteryMethodEloq->errors()->messages()) {
                return ['success' => false, 'message' => $lotteryMethodEloq->errors()->messages()];
            }
        }
        return ['success' => true];
    }
}
