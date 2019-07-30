<?php

namespace App\Http\SingleActions\Backend\System;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SystemUploadPiclAction
{

    /**
     * 图片上传
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $imageObj = new ImageArrange();
        $file = $inputDatas['pic'];
        $folderName = $inputDatas['folder_name'];
        $depositPath = $imageObj->depositPath($folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        $pic = $imageObj->uploadImg($file, $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        $pic['expire_time'] = Carbon::now()->addHours(6)->timestamp; //设置图片过期时间6小时
        $hourToStore = 24 * 2;
        $expiresAt = Carbon::now()->addHours($hourToStore);
        if (Cache::has('cache_pic')) {
            $cachePic = Cache::get('cache_pic');
        }
        $cachePic[$pic['name']] = $pic;
        Cache::put('cache_pic', $cachePic, $expiresAt);
        return $contll->msgOut(true, $pic);
    }

}
