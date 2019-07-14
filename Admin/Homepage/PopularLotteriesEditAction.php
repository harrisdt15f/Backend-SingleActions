<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:34:31
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:05:29
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PopularLotteriesEditAction
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
     * 编辑热门彩票
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        //检查该热门类型是否存在重复彩票
        $checkData = $this->model::where('lotteries_id', $inputDatas['lotteries_id'])->where('id', '!=', $inputDatas['id'])->first();
        if ($checkData === true) {
            return $contll->msgOut(false, [], '102000');
        }
        $pastDataEloq = $this->model::find($inputDatas['id']);
        //修改了图片的操作
        if (isset($inputDatas['pic'])) {
            $pastPic = $pastDataEloq->pic_path;
            $imageObj = new ImageArrange();
            $depositPath = $imageObj->depositPath('popular_lotteries', $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
            $pic = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
            if ($pic['success'] === false) {
                return $contll->msgOut(false, [], '400', $pic['msg']);
            }
            $pastDataEloq->pic_path = '/' . $pic['path'];
        }
        try {
            $pastDataEloq->lotteries_id = $inputDatas['lotteries_id'];
            $pastDataEloq->save();
            if (isset($pastPic)) {
                $imageObj->deletePic(substr($pastPic, 1));
            }
            //清除首页热门彩票缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            if (isset($pic)) {
                $imageObj->deletePic($pic['path']);
            }
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
