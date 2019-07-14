<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 14:24:46
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:12:36
 */
namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Activity\FrontendActivityContent;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ActivityInfosSortAction
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
     * 活动排序
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        try {
            //上拉排序
            if ($inputDatas['sort_type'] == 1) {
                $stationaryData = $this->model::find($inputDatas['front_id']);
                $stationaryData->sort = $inputDatas['front_sort'];
                $this->model::where('sort', '>=', $inputDatas['front_sort'])->where('sort', '<', $inputDatas['rearways_sort'])->increment('sort');
                //下拉排序
            } elseif ($inputDatas['sort_type'] == 2) {
                $stationaryData = $this->model::find($inputDatas['rearways_id']);
                $stationaryData->sort = $inputDatas['rearways_sort'];
                $this->model::where('sort', '>', $inputDatas['front_sort'])->where('sort', '<=', $inputDatas['rearways_sort'])->decrement('sort');
            }
            $stationaryData->save();
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
