<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
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
        $imageObj = new ImageArrange();
        $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        $icon = $imageObj->uploadImg($inputDatas['icon'], $depositPath);
        if ($icon['success'] === false) {
            return $contll->msgOut(false, [], '400', $icon['msg']);
        }
        $lotteryEloq = LotteryList::find($inputDatas['id']);
        $pastIcon = $lotteryEloq->icon_path;
        $lotteryEloq->icon_path = '/' . $icon['path'];
        $lotteryEloq->save();
        if ($lotteryEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $lotteryEloq->errors()->messages());
        }
        $imageObj->deletePic(substr($pastIcon, 1));
        return $contll->msgOut(true);
    }
}
