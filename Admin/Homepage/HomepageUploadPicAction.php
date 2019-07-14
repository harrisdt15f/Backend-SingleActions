<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:02:46
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:05:04
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageUploadPicAction
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
     * 修改首页模块下的图片
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastData = $this->model::where('en_name', $inputDatas['en_name'])->first();
        $imageObj = new ImageArrange();
        $depositPath = $imageObj->depositPath($inputDatas['en_name'], $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        $pic = $imageObj->uploadImg($inputDatas['pic'], $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        $pastLogoPath = $pastData->value;
        try {
            $pastData->value = '/' . $pic['path'];
            $pastData->save();
            //删除原图
            if ($pastLogoPath !== null) {
                $imageObj->deletePic(substr($pastLogoPath, 1));
            }
            //删除前台首页缓存
            $contll->deleteCache($pastData->en_name);
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $imageObj->deletePic($pic['path']);
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
