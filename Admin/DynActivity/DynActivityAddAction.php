<?php

namespace App\Http\SingleActions\Backend\Admin\DynActivity;

use App\Http\Controllers\BackendApi\Admin\DynActivity\DynActivityController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\DynActivity\BackendDynActivityList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DynActivityAddAction
{
    protected $model;

    public function __construct(BackendDynActivityList $backendDynActivityList)
    {
        $this->model = $backendDynActivityList;
    }

    public function execute(DynActivityController $contll, array $inputDatas) :JsonResponse
    {
        try {
            $addDatas = $inputDatas;
            if (strtotime($inputDatas['start_time']) >= strtotime($inputDatas['end_time'])) {
                return $contll->msgOut(false, [], '400', '开始时间必须小与结束时间');
            }
            $imageObj = new ImageArrange();
            if (isset($inputDatas['pc_pic'])) {
                $pcDepositPath = $imageObj->depositPath($contll->folderName.'/pc', $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
                unset($addDatas['pc_pic']);
                $previewPcPic = $imageObj->uploadImg($inputDatas['pc_pic'], $pcDepositPath);
                if ($previewPcPic['success'] === false) {
                    return $contll->msgOut(false, [], '400', $previewPcPic['msg']);
                }
                $addDatas['pc_pic'] = '/' . $previewPcPic['path'];
            }
            if (isset($inputDatas['wap_pic'])) {
                $wapDepositPath = $imageObj->depositPath($contll->folderName.'/wap', $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
                unset($addDatas['wap_pic']);
                $previewWapPic = $imageObj->uploadImg($inputDatas['wap_pic'], $wapDepositPath);
                if ($previewWapPic['success'] === false) {
                    return $contll->msgOut(false, [], '400', $previewWapPic['msg']);
                }
                $addDatas['wap_pic'] = '/' . $previewWapPic['path'];
            }
            $addDatas['sort'] = $inputDatas['sort'] ?? 1;
            $addDatas['status'] = $inputDatas['status'] ?? $this->model::STATUS_DISABLE;
            $dynActivityEloq = new $this->model();
            $dynActivityEloq->fill($addDatas);
            $dynActivityEloq->save();
            if ($dynActivityEloq->errors()->messages()) {
                $this->deletePic($imageObj , $previewPcPic , $previewWapPic);
                return $contll->msgOut(false, [], '400', $dynActivityEloq->errors()->messages());
            }
            return $contll->msgOut(true);
        } catch (\Exception $e) {
            $this->deletePic($imageObj , $previewPcPic , $previewWapPic);
            if (!env('APP_DEBUG')) {
                Log::info($e->getMessage());
                return $contll->msgOut(false,[],'400','系统异常');
            }
            return $contll->msgOut(false,[],'400',$e->getMessage());
        }
    }

    private function deletePic($imageObj, $previewPcPic, $previewWapPic)
    {
        if (isset($previewPcPic['path'])) {//上传失败   删除前面上传的图片
            $imageObj->deletePic($previewPcPic['path']);
        }
        if (isset($previewWapPic['path'])) {//上传失败   删除前面上传的图片
            $imageObj->deletePic($previewWapPic['path']);
        }
    }
}
