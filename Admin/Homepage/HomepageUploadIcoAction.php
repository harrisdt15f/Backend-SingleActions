<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:12:39
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:04:50
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageUploadIcoAction
{
    protected $model;

    /**
     * @param  FrontendAllocatedModel  $frontendAllocatedModel
     */
    public function __construct(FrontendAllocatedModel $frontendAllocatedModel)
    {
        $this->model = $frontendAllocatedModel;
    }

    /**
     * 上传前台网站头ico
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastData = $this->model::where('en_name', 'frontend.ico')->first();
        $imageObj = new ImageArrange();
        $folderName = 'frontend';
        $depositPath = $imageObj->depositPath($folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name) . '/ico';
        $ico = $imageObj->uploadImg($inputDatas['ico'], $depositPath);
        $pastIco = $pastData->value;
        try {
            $pastData->value = '/' . $ico['path'];
            $pastData->save();
            //删除前台首页缓存
            $contll->deleteCache($pastData->en_name);
            //删除原图
            if ($pastIco !== null) {
                $imageObj->deletePic(substr($pastIco, 1));
            }
            return $contll->msgOut(true);
        } catch (Exception $e) {
            //删除上传成功的图片
            $imageObj->deletePic($ico['path']);
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
