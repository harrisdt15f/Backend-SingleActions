<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:34:58
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:01:09
 */
namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NoticeAddAction
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
     * 添加公告
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $addData = $inputDatas;
        $addData['admin_id'] = $contll->partnerAdmin->id;
        $maxSort = $this->model::select('sort')->max('sort');
        $addData['sort'] = ++$maxSort;
        try {
            $noticeEloq = new $this->model;
            $noticeEloq->fill($addData);
            $noticeEloq->save();
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
