<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 18:01:49
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:20:12
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryFnfBetableList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularMethodsEditAction
{
    protected $model;

    /**
     * @param  FrontendLotteryFnfBetableList  $frontendLotteryFnfBetableList
     */
    public function __construct(FrontendLotteryFnfBetableList $frontendLotteryFnfBetableList)
    {
        $this->model = $frontendLotteryFnfBetableList;
    }

    /**
     * 热门彩票二 编辑热门玩法
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        //彩种是否已存在
        $isExistLottery = $this->model::where('lotteries_id', $inputDatas['lotteries_id'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($isExistLottery === true) {
            return $contll->msgOut(false, [], '100600');
        }
        try {
            $pastDataEloq = $this->model::find($inputDatas['id']);
            $contll->editAssignment($pastDataEloq, $inputDatas);
            $pastDataEloq->save();
            //清除首页热门玩法缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
