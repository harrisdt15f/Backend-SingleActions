<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 15:46:28
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:16:59
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendPageBanner;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomepageBannerSortAction
{
    protected $model;

    /**
     * @param  FrontendPageBanner  $frontendPageBanner
     */
    public function __construct(FrontendPageBanner $frontendPageBanner)
    {
        $this->model = $frontendPageBanner;
    }

    /**
     * 首页轮播图排序
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
            //清除首页banner缓存
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
