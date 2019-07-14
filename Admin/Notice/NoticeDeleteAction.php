<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:45:24
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:21:40
 */
namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NoticeDeleteAction
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
     * 删除公告
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        //sort
        $sort = $pastDataEloq->sort;
        DB::beginTransaction();
        try {
            $pastDataEloq->delete();
            $this->model::where('sort', '>', $sort)->decrement('sort');
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
