<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 15:29:50
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:04:39
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendPageBanner;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageBannerEditAction
{
    protected $model;

    /**
     * @param  FrontendPageBanner  $frontendPageBanner
     */
    public function __construct(FrontendPageBanner $frontendPageBanner)
    {
        $this->model = $frontendPageBanner;
    }

    /**
     * 编辑首页轮播图
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $checkTitle = $this->model::where('title', $inputDatas['title'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($checkTitle === true) {
            return $contll->msgOut(false, [], '101800');
        }
        $pastData = $this->model::find($inputDatas['id']);
        $editData = $inputDatas;
        unset($editData['pic']);
        //如果要修改图片  删除原图  上传新图
        if (isset($inputDatas['pic'])) {
            $imageObj = new ImageArrange();
            $imageObj->deletePic(substr($pastData['pic_path'], 1));
            $imageObj->deletePic(substr($pastData['thumbnail_path'], 1));
            $depositPath = $imageObj->depositPath($contll->folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
            $picData = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
            if ($picData['success'] === false) {
                return $contll->msgOut(false, [], $picData['code']);
            }
            //上传缩略图
            $thumbnail = $imageObj->creatThumbnail($picData['path'], 100, 200);
            $editData['pic_path'] = '/' . $picData['path'];
            $editData['thumbnail_path'] = '/' . $thumbnail;
        }
        try {
            $contll->editAssignment($pastData, $editData);
            $pastData->save();
            //清除首页banner缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
