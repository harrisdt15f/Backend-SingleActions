<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryNoticeList;
use Illuminate\Http\JsonResponse;

class LotteryNoticeEditAction
{
    /**
     * 编辑开奖公告的彩种
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastEloq = FrontendLotteryNoticeList::find($inputDatas['id']);
        if (isset($inputDatas['icon'])) {
            $pastIcon = $pastEloq->icon_path; //保存原图路径  修改成功后删除
            $imageObj = new ImageArrange();
            $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
            //进行上传
            $pic = $imageObj->uploadImg($inputDatas['icon'], $depositPath);
            if ($pic['success'] === false) {
                return $contll->msgOut(false, [], '400', $pic['msg']);
            }
            $pastEloq->icon_path = '/' . $pic['path'];
        }
        $pastEloq->lotteries_id = $inputDatas['lotteries_id'] ?? $pastEloq->lotteries_id;
        $pastEloq->cn_name = $inputDatas['cn_name'] ?? $pastEloq->cn_name;
        $pastEloq->status = $inputDatas['status'] ?? $pastEloq->status;
        $pastEloq->save();
        if ($pastEloq->errors()->messages()) {
            if (isset($pic['path'])) {
                $imageObj->deletePic($pic['path']); //修改数据失败，删除新上传的图片
            }
            return $contll->msgOut(false, [], '400', $pastEloq->errors()->messages());
        }
        if (isset($pastIcon)) {
            $imageObj->deletePic(substr($pastIcon, 1)); //修改数据成功后 删除原图
        }
        return $contll->msgOut(true);
    }
}
