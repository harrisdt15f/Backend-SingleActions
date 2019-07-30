<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\CacheRelated;
use App\Lib\Common\ImageArrange;
use App\Models\Game\Lottery\LotteryList;
use Illuminate\Http\JsonResponse;

class LotteriesEditIconAction
{
    /**
     * 编辑彩种icon
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $lotteryEloq = LotteryList::find($inputDatas['id']);
        $pastPic = $lotteryEloq->icon_path;
        $lotteryEloq->icon_path = $inputDatas['icon_path'];
        $lotteryEloq->save();
        if ($lotteryEloq->errors()->messages()) {
            $contll->msgOut(false, [], '400', $lotteryEloq->errors()->messages());
        }
        ImageArrange::deletePic($pastPic);
        CacheRelated::deleteCachePic($inputDatas['icon_name']); //从定时清理的缓存图片中移除上传成功的图片
        return $contll->msgOut(true);
    }
}
