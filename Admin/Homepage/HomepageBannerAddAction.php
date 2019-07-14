<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 15:22:11
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:04:22
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendPageBanner;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageBannerAddAction
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
     * 添加首页轮播图
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $imageObj = new ImageArrange();
        $folderName = $contll->folderName;
        $depositPath = $imageObj->depositPath($folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        $pic = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        //生成缩略图
        $thumbnail = $imageObj->creatThumbnail($pic['path'], 100, 200);
        $addData = $inputDatas;
        unset($addData['pic']);
        $addData['pic_path'] = '/' . $pic['path'];
        $addData['thumbnail_path'] = '/' . $thumbnail;
        //sort
        $maxSort = $this->model::select('sort')->max('sort');
        $addData['sort'] = ++$maxSort;
        try {
            $rotationChartEloq = new $this->model;
            $rotationChartEloq->fill($addData);
            $rotationChartEloq->save();
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
