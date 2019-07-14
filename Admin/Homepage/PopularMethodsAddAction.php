<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:58:34
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:20:06
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryFnfBetableList;
use Exception;
use Illuminate\Http\JsonResponse;

class PopularMethodsAddAction
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
     * 热门彩票二 添加热门彩票
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        //sort
        $maxSort = $this->model::select('sort')->max('sort');
        $sort = ++$maxSort;
        $addData = [
            'lotteries_id' => $inputDatas['lotteries_id'],
            'method_id' => $inputDatas['method_id'],
            'sort' => $sort,
        ];
        try {
            $popularLotteriesEloq = new $this->model;
            $popularLotteriesEloq->fill($addData);
            $popularLotteriesEloq->save();
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
