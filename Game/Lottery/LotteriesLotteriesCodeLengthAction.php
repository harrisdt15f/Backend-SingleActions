<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 17:54:03
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 17:55:58
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;

class LotteriesLotteriesCodeLengthAction
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
     * 奖期录号规则
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = $this->model::select('en_name', 'code_length', 'valid_code', 'lottery_type')->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
