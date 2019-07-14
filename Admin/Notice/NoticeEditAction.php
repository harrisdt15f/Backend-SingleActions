<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:41:24
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:21:33
 */
namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use Exception;
use Illuminate\Http\JsonResponse;

class NoticeEditAction
{
    protected $model;

    /**
     * @param  FrontendMessageNotice  $frontendMessageNotice
     */
    public function __construct(FrontendMessageNotice $frontendMessageNotice)
    {
        $this->model = $frontendMessageNotice;
    }

    /**
     * 编辑公告
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $checkTitle = $this->model::where('title', $inputDatas['title'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($checkTitle === true) {
            return $contll->msgOut(false, [], '102100');
        }
        try {
            $pastEloq = $this->model::find($inputDatas['id']);
            $contll->editAssignment($pastEloq, $inputDatas);
            $pastEloq->save();
            //删除前台首页缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
