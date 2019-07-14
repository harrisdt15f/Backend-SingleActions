<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 15:55:37
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 15:59:31
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

class LotteriesSeriesListsAction
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
     * 获取系列接口
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $seriesData = Config::get('game.main.series');
        return $contll->msgOut(true, $seriesData);
    }
}
