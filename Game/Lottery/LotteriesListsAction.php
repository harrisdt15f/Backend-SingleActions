<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 16:00:38
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:39:09
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

class LotteriesListsAction
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
     * 获取彩种接口
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $lotteries = $this->model::where([
            ['series_id', '=', $inputDatas['series_id']],
            ['status', '=', 1],
        ])->with([
            'issueRule' => function ($query) {
                $query->select('id', 'lottery_id', 'lottery_name', 'begin_time', 'end_time', 'issue_seconds',
                    'first_time', 'adjust_time', 'encode_time', 'issue_count', 'status', 'created_at', 'updated_at');
            },
        ])->get()->toArray();
        return $contll->msgOut(true, $lotteries);
    }
}
