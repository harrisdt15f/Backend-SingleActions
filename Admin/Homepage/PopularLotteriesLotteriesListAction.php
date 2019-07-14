<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:49:13
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:19:47
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;

class PopularLotteriesLotteriesListAction
{

    /**
     * 选择的彩种列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $lotteries = LotteryList::select('id', 'cn_name', 'en_name')->get();
        return $contll->msgOut(true, $lotteries);
    }
}
