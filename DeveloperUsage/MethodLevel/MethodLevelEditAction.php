<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 15:35:17
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 15:54:32
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\MethodLevel;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\MethodLevel\LotteryMethodsWaysLevel;
use Exception;
use Illuminate\Http\JsonResponse;

class MethodLevelEditAction
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
     * 编辑玩法等级
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        //检查玩法等级
        $isExistMethodLevel = $this->model::where([
            ['method_id', $pastDataEloq->method_id],
            ['level', $inputDatas['level']],
            ['id', '!=', $inputDatas['id']],
        ])->exists();
        if ($isExistMethodLevel === true) {
            return $contll->msgOut(false, [], '102200');
        }
        try {
            $contll->editAssignment($pastDataEloq, $inputDatas);
            $pastDataEloq->save();
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
