<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\MethodLevel;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\MethodLevel\LotteryMethodsWaysLevel;
use Illuminate\Http\JsonResponse;

class MethodLevelDeleteAction
{
    protected $model;

    /**
     * @param  LotteryMethodsWaysLevel  $lotteryMethodsWaysLevel
     */
    public function __construct(LotteryMethodsWaysLevel $lotteryMethodsWaysLevel)
    {
        $this->model = $lotteryMethodsWaysLevel;
    }

    /**
     * 删除玩法等级
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $methodLevelEloq = $this->model::find($inputDatas['id']);
        $methodLevelEloq->delete();
        if ($methodLevelEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '', $methodLevelEloq->errors()->messages());
        }
        //删除玩法等级列表缓存
        $contll->deleteCache();
        return $contll->msgOut(true);
    }
}
