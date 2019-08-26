<?php

namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Lib\BaseCache;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\FrontendActivityContent;
use Illuminate\Http\JsonResponse;

class ActivityInfosEditAction
{
    use BaseCache;

    protected $model;

    /**
     * @param  FrontendActivityContent  $frontendActivityContent
     */
    public function __construct(FrontendActivityContent $frontendActivityContent)
    {
        $this->model = $frontendActivityContent;
    }

    /**
     * 编辑活动
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $editData = $inputDatas;
        //如果修改了图片 上传新图片
        if (isset($inputDatas['pic']) || isset($inputDatas['preview_pic'])) {
            $imageObj = new ImageArrange();
            $depositPath = $imageObj->depositPath(
                $contll->folderName,
                $contll->currentPlatformEloq->platform_id,
                $contll->currentPlatformEloq->platform_name
            );
            if (isset($inputDatas['pic'])) {
                unset($editData['pic']);
                $pastPic = $pastDataEloq->pic_path;
                $picdata = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
                if ($picdata['success'] === false) {
                    return $this->msgOut(false, [], '400', $picdata['msg']);
                }
                $pastDataEloq->pic_path = '/' . $picdata['path'];
            }
            if (isset($inputDatas['preview_pic'])) {
                unset($editData['preview_pic']);
                $pastPreviewPic = $pastDataEloq->preview_pic_path;
                $previewPic = $imageObj->uploadImg($inputDatas['preview_pic'], $depositPath);
                if ($previewPic['success'] === false) {
                    return $this->msgOut(false, [], '400', $previewPic['msg']);
                }
                $pastDataEloq->preview_pic_path = '/' . $previewPic['path'];
            }
        }
        $contll->editAssignment($pastDataEloq, $editData);
        $pastDataEloq->save();
        if ($pastDataEloq->errors()->messages()) {
            return $this->msgOut(false, [], '400', $pastDataEloq->errors()->messages());
        }
        if (isset($inputDatas['pic'])) {
            $imageObj->deletePic(substr($pastPic, 1));
        }
        if (isset($inputDatas['preview_pic'])) {
            $imageObj->deletePic(substr($pastPreviewPic, 1));
        }
        self::deleteTagsCache($contll->redisKey); //删除前台活动缓存
        return $contll->msgOut(true);
    }
}
