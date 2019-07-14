<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 16:12:52
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:40:07
 */
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
        $eloqM = $contll->modelWithNameSpace($contll->lotteryIssueEloq);
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
        $searchAbleFields = ['lottery_id', 'issue'];
        $orderFields = 'begin_time';
        $orderFlow = 'asc';
        $fixedJoin = 1;
        $withTable = 'lottery';
        $searchFieldArr = ['issue']; //存在此搜索字段  不插入time_condtions条件
        $isExistField = arr::has($contll->inputs, $searchFieldArr);
        if ($isExistField === false) {
            //如果直接按时间段搜索时执行
            if (isset($contll->inputs['begin_time'], $contll->inputs['end_time'])) {
                $timeCondtions = '[["end_time",">=",' . $contll->inputs['begin_time'] . '],["end_time","<=",' . $contll->inputs['end_time'] . ']]';
            } else {
                $timeToSubstract = 0; // 秒
                //选定彩种并展示已过期的期数
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
            }
            $contll->inputs['time_condtions'] = $contll->inputs['time_condtions'] ?? $timeCondtions; // 从现在开始。如果。没有时间字段的话，就用当前时间以上的显示
        }
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $data);
    }
}
