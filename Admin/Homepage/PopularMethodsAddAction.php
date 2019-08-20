<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
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
        $popularLotteriesEloq = new $this->model;
        $popularLotteriesEloq->fill($addData);
        $popularLotteriesEloq->save();
        if ($popularLotteriesEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '', $popularLotteriesEloq->errors()->messages());
        }
        //清除首页热门玩法缓存
        $contll->deleteCache();
        return $contll->msgOut(true);
    }
}
