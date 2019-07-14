<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 17:18:56
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:41:03
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;

class LotteriesLotteriesSwitchAction
{
    protected $model;

    /**
     * @param  LotteryList  $lotteryList
     */
    public function __construct(LotteryList $lotteryList)
    {
        $this->model = $lotteryList;
    }

    /**
     * 彩种开关
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $lotteriesEloq = $this->model::find($inputDatas['id']);
        try {
            $lotteriesEloq->status = $inputDatas['status'];
            $lotteriesEloq->save();
            $lotteriesEloq->lotteryInfoCache(); //更新首页lotteryInfo缓存
            //清理彩种玩法缓存
            $contll->clearMethodCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
