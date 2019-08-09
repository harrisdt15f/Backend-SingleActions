<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
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
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $contll->editAssignment($pastDataEloq, $inputDatas);
        $pastDataEloq->save();
        if ($pastDataEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $pastDataEloq->errors()->messages());
        }
        //清除首页热门玩法缓存
        $contll->deleteCache();
        return $contll->msgOut(true);
    }
}
