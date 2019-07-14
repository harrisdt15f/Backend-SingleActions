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
        if (isset($inputDatas['pic'])) {
            unset($editData['pic']);
            $pastPic = $pastDataEloq->pic_path;
            $pastThumbnail = $pastDataEloq->thumbnail_path;
            //接收文件信息
            $imageObj = new ImageArrange();
            $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
            //进行上传
            $picdata = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
            if ($picdata['success'] === false) {
                return $this->msgOut(false, [], '400', $picdata['msg']);
            }
            $pastDataEloq->pic_path = '/' . $picdata['path'];
            //生成缩略图
            $pastDataEloq->thumbnail_path = '/' . $imageObj->creatThumbnail($picdata['path'], 100, 200);
        }
        $contll->editAssignment($pastDataEloq, $editData);
        try {
            $pastDataEloq->save();
            if (isset($inputDatas['pic'])) {
                //删除原图片
                $imageObj->deletePic(substr($pastPic, 1));
                $imageObj->deletePic(substr($pastThumbnail, 1));
            }
            //删除前台首页缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
