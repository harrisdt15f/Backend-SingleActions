<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 13:44:02
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:03:30
 */
namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\FrontendActivityContent;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ActivityInfosEditAction
{
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
        $issetTitle = $this->model::where('title', $inputDatas['title'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($issetTitle === true) {
            return $contll->msgOut(false, [], '100300');
        }
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $editData = $inputDatas;
        //如果修改了图片 上传新图片
        if (isset($inputDatas['pic']) || isset($inputDatas['preview_pic'])) {
            $imageObj = new ImageArrange();
            $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
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
                $previewpreviewPic = $pastDataEloq->preview_pic_path;
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
        if (isset($inputDatas['pic'])) {
            $imageObj->deletePic(substr($previewpreviewPic, 1));
        }
        $contll->deleteCache(); //删除前台首页缓存
        return $contll->msgOut(true);
    }
}
