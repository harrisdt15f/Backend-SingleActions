<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 14:02:22
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:12:29
 */
namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\FrontendActivityContent;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ActivityInfosDeleteAction
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
     * 删除活动
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        DB::beginTransaction();
        try {
            $this->model::where('id', $inputDatas['id'])->delete();
            //排序
            $this->model::where('sort', '>', $pastDataEloq->sort)->decrement('sort');
            DB::commit();
            //删除图片
            $imageObj = new ImageArrange();
            $imageObj->deletePic(substr($pastDataEloq->pic_path, 1));
            $imageObj->deletePic(substr($pastDataEloq->thumbnail_path, 1));
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
