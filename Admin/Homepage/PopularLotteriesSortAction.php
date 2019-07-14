<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:45:23
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:19:42
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PopularLotteriesSortAction
{
    protected $model;

    /**
     * @param  FrontendLotteryRedirectBetList  $frontendLotteryRedirectBetList
     */
    public function __construct(FrontendLotteryRedirectBetList $frontendLotteryRedirectBetList)
    {
        $this->model = $frontendLotteryRedirectBetList;
    }

    /**
     * 热门彩票拉动排序
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
            } elseif ($inputDatas['sort_type'] == 2) {
                //下拉排序
                $stationaryData = $this->model::find($inputDatas['rearways_id']);
                $stationaryData->sort = $inputDatas['rearways_sort'];
                $this->model::where('sort', '>', $inputDatas['front_sort'])->where('sort', '<=', $inputDatas['rearways_sort'])->decrement('sort');
            }
            $stationaryData->save();
            DB::commit();
            //清除首页热门彩票缓存
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
