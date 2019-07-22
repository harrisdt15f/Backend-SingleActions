<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:41:24
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:21:33
 */
namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use Illuminate\Http\JsonResponse;

class NoticeEditAction
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
     * 编辑 公告|站内信
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastEloq = $this->model::find($inputDatas['id']);
        $contll->editAssignment($pastEloq, $inputDatas);
        $pastEloq->save();
        if ($pastEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $pastEloq->errors()->messages());
        }
        return $contll->msgOut(true);
    }
}
