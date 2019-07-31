<?php

namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use Illuminate\Http\JsonResponse;

class NoticeDetailAction
{
    protected $model;

    /**
     * @param  FrontendMessageNoticesContent  $frontendMessageNoticesContent
     */
    public function __construct(FrontendMessageNoticesContent $frontendMessageNoticesContent)
    {
        $this->model = $frontendMessageNoticesContent;
    }

    /**
     * 公告列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $searchAbleFields = ['type'];
        $orderFields = 'id';
        $orderFlow = 'desc';
        $datas = $contll->generateSearchQuery($this->model, $searchAbleFields, 0, null, null, $orderFields, $orderFlow);
        return $contll->msgOut(true, $datas);
    }
}
