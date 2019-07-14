<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 16:10:47
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:17:06
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Activity\FrontendActivityContent;
use Illuminate\Http\JsonResponse;

class HomepageActivityListAction
{
    /**
     * 操作轮播图时获取的活动列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $activityList = FrontendActivityContent::select('id', 'title')->where('status', 1)->get()->toArray();
        return $contll->msgOut(true, $activityList);
    }
}
