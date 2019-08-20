<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\MethodLevel;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\MethodLevel\LotteryMethodsWaysLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MethodLevelDetailAction
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
     * 玩法等级管理列表
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        if (Cache::has('methodLeveDetail')) {
            $data = Cache::get('methodLeveDetail');
        } else {
            $methodLevelEloq = new $this->model;
            $data = $methodLevelEloq->methodLevelDetail();
            Cache::forever('methodLeveDetail', $data);
        }
        return $contll->msgOut(true, $data);
    }
}
