<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 15:45:28
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:37:52
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\MethodLevel;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\MethodLevel\LotteryMethodsWaysLevel;
use Exception;
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
        try {
            $this->model::find($inputDatas['id'])->delete();
            //删除玩法等级列表缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
