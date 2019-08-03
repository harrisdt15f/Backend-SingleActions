<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class LotteriesIssueListsAction
{
    protected $model;

    /**
     * @param  LotteryList  $lotteryList
     */
    public function __construct(LotteryList $lotteryList)
    {
        $this->model = $lotteryList;
    }

    /**
     * 获取奖期列表接口。
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $seriesId = $contll->inputs['series_id'] ?? '';
//        {"method":"whereIn","key":"id","value":["cqssc","xjssc","hljssc","zx1fc","txffc"]}
        //        $extraWhereConditions = Arr::wrap(json_decode($this->inputs['extra_where'], true));
        if (!empty($seriesId)) {
            $lotteryEnNames = $this->model::where('series_id', $seriesId)->get(['en_name']);
            foreach ($lotteryEnNames as $lotteryIthems) {
                $tempLotteryId[] = $lotteryIthems->en_name;
            }
            $contll->inputs['extra_where']['method'] = 'whereIn';
            $contll->inputs['extra_where']['key'] = 'lottery_id';
            $contll->inputs['extra_where']['value'] = $tempLotteryId;
        }
        if (!isset($contll->inputs['time_condtions'])) {
            $timeToSubstract = 0; // 不存在时间段搜索时，默认返回现在还未结束的奖期
            //选定彩种并选择了展示已过期的期数时  重新计算哪个时间之后的奖期
            if (isset($contll->inputs['lottery_id'], $contll->inputs['previous_number'])) {
                $lotteryEloq = LotteryList::where('en_name', $contll->inputs['lottery_id'])->first();
                if ($lotteryEloq === null) {
                    return $contll->msgOut(false, [], '101700');
                }
                $issueSeconds = $lotteryEloq->issueRule->issue_seconds;
                $timeToSubstract = $issueSeconds * $contll->inputs['previous_number'];
            }
            $afewMinutes = Carbon::now()->subSeconds($timeToSubstract)->timestamp;
            $timeCondtions = '[["end_time",">=",' . $afewMinutes . ']]';
            $contll->inputs['time_condtions'] = $timeCondtions;
        }
        $eloqM = $contll->modelWithNameSpace($contll->lotteryIssueEloq);
        $searchAbleFields = ['lottery_id', 'issue'];
        $fixedJoin = 1;
        $withTable = 'lottery';
        $orderFields = 'begin_time';
        $orderFlow = 'asc';
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $data);
    }
}
