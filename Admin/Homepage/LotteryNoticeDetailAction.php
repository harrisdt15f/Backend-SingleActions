<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryNoticeList;
use Illuminate\Http\JsonResponse;

class LotteryNoticeDetailAction
{
    /**
     * 开奖公告的彩种列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $data = FrontendLotteryNoticeList::select('id', 'lotteries_id', 'icon_path', 'status', 'sort')->orderBy('sort', 'asc')->get()->toArray();
        return $contll->msgOut(true, $data);
    }
}