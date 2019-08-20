<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\MethodLevel;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\MethodLevel\LotteryMethodsWaysLevel;
use Illuminate\Http\JsonResponse;

class MethodLevelAddAction
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
     * 添加玩法等级
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        //检查玩法等级
        $isExistMethodLevel = $this->model::where([
            ['method_id', $inputDatas['method_id']],
            ['series_id', $inputDatas['series_id']],
            ['level', $inputDatas['level']],
        ])->exists();
        if ($isExistMethodLevel === true) {
            return $contll->msgOut(false, [], '102200');
        }
        $methodLevelEloq = new $this->model;
        $methodLevelEloq->fill($inputDatas);
        $methodLevelEloq->save();
        if ($methodLevelEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '', $methodLevelEloq->errors()->messages());
        }
        //删除玩法等级列表缓存
        $contll->deleteCache();
        return $contll->msgOut(true);
    }
}
