<?php

namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Admin\Activity\FrontendActivityContent;
use Illuminate\Http\JsonResponse;

class ActivityInfosDetailAction
{
    protected $model;

    /**
     * @param  FrontendActivityContent  $frontendActivityContent
     */
    public function __construct(FrontendActivityContent $frontendActivityContent)
    {
        $this->model = $frontendActivityContent;
    }

    /**
     * 活动列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $searchAbleFields = ['title', 'type', 'status', 'admin_name', 'is_time_interval'];
        $orderFields = 'sort';
        $orderFlow = 'asc';
        $contll->type = $inputDatas['type'];
        $datas = $contll->generateSearchQuery($this->model, $searchAbleFields, 0, null, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $datas);
    }

}
