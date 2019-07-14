<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 11:51:13
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:03:16
 */
namespace App\Http\SingleActions\Backend\Admin\Activity;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\FrontendActivityContent;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

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
        //进行上传
        $pic = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        //生成缩略图
        $thumbnailPath = $imageObj->creatThumbnail($pic['path'], 100, 200);
        //sort
        $maxSort = $this->model::select('sort')->max('sort');
        $sort = ++$maxSort;
        $addDatas = $inputDatas;
        unset($addDatas['pic']);
        $addDatas['sort'] = $sort;
        $addDatas['pic_path'] = '/' . $pic['path'];
        $addDatas['thumbnail_path'] = '/' . $thumbnailPath;
        $addDatas['admin_id'] = $contll->partnerAdmin->id;
        $addDatas['admin_name'] = $contll->partnerAdmin->name;
        try {
            $configure = new $this->model();
            $configure->fill($addDatas);
            $configure->save();
            //删除前台首页缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $imageObj->deletePic($pic['path']);
            $imageObj->deletePic($thumbnailPath);
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $this->msgOut(false, [], $sqlState, $msg);
        }
    }
}
