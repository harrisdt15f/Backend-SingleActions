<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:29:43
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:05:15
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PopularLotteriesAddAction
{
    protected $model;

    /**
     * @param  FrontendLotteryRedirectBetList  $frontendLotteryRedirectBetList
     */
    public function __construct(FrontendLotteryRedirectBetList $frontendLotteryRedirectBetList)
    {
        $this->model = $frontendLotteryRedirectBetList;
    }

    /**
     * 添加热门彩票一
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $imageObj = new ImageArrange();
        $depositPath = $imageObj->depositPath('popular_lotteries', $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        //sort
        $maxSort = $this->model::select('sort')->max('sort');
        $sort = ++$maxSort;
        $pic = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        $addData = [
            'lotteries_id' => $inputDatas['lotteries_id'],
            'sort' => $sort,
            'pic_path' => '/' . $pic['path'],
        ];
        try {
            $popularLotteriesEloq = new $this->model;
            $popularLotteriesEloq->fill($addData);
            $popularLotteriesEloq->save();
            //清除首页热门彩票缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $imageObj->deletePic($pic['path']);
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
