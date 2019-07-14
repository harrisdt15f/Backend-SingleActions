<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:52:26
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:21:52
 */
namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NoticeTopAction
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
     * 公告置顶
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $sort = $pastDataEloq->sort;
        DB::beginTransaction();
        try {
            $pastDataEloq->sort = 1;
            $pastDataEloq->save();
            $this->model::where('sort', '<', $sort)->where('id', '!=', $inputDatas['id'])->increment('sort');
            DB::commit();
            //删除前台首页缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
