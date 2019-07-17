<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryNoticeList;
use Illuminate\Http\JsonResponse;

class LotteryNoticeAddAction
{
    /**
     * 添加开奖公告的彩种
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $imageObj = new ImageArrange();
        $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        $pic = $imageObj->uploadImg($inputDatas['icon'], $depositPath); //进行上传
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        $maxSort = FrontendLotteryNoticeList::select('sort')->max('sort');
        $sort = ++$maxSort;
        $addData = [
            'lotteries_id' => $inputDatas['lotteries_id'],
            'cn_name' => $inputDatas['cn_name'],
            'status' => $inputDatas['status'],
            'icon_path' => '/' . $pic['path'],
            'sort' => $sort,
        ];
        $lotteryNoticeELoq = new FrontendLotteryNoticeList();
        $lotteryNoticeELoq->fill($addData);
        $lotteryNoticeELoq->save();
        if ($lotteryNoticeELoq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $lotteryNoticeELoq->errors()->messages());
        }
        return $contll->msgOut(true);
    }
}
