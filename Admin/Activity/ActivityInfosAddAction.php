<?php

namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\FrontendActivityContent;
use Illuminate\Http\JsonResponse;

class ActivityInfosAddAction
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
     * 添加活动
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        //接收文件信息
        $imageObj = new ImageArrange();
        $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        $addDatas = $inputDatas;
        unset($addDatas['pic'], $addDatas['preview_pic']);
        //进行上传
        $previewPic = $imageObj->uploadImg($inputDatas['preview_pic'], $depositPath);

        if ($previewPic['success'] === false) {
            return $contll->msgOut(false, [], '400', $previewPic['msg']);
        }
        $addDatas['preview_pic_path'] = '/' . $previewPic['path'];
        if (isset($inputDatas['pic'])) {
            $pic = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
            if ($pic['success'] === false) {
                $imageObj->deletePic($previewPic['path']); //此次上传失败   删除前面上传的图片
                return $contll->msgOut(false, [], '400', $pic['msg']);
            }
            $addDatas['pic_path'] = '/' . $pic['path'];
        }
        $maxSort = $this->model::select('sort')->max('sort');
        $sort = ++$maxSort; //sort
        $addDatas['sort'] = $sort;
        $addDatas['admin_id'] = $contll->partnerAdmin->id;
        $addDatas['admin_name'] = $contll->partnerAdmin->name;
        $addDatas['type'] = $inputDatas['type'];
        $activityEloq = new $this->model();
        $activityEloq->fill($addDatas);
        $activityEloq->save();
        if ($activityEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $activityEloq->errors()->messages());
        }
        //删除前台首页缓存
        $contll->deleteCache();
        return $contll->msgOut(true);
    }
}
