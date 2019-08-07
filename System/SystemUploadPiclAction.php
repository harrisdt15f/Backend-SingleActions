<?php

namespace App\Http\SingleActions\Backend\System;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\User\UserPublicAvatar;
use function Couchbase\defaultDecoder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SystemUploadPiclAction
{
    //头像上传文件夹
    public const USER_PUBLIC_AVATARS = "avatars";

    /**
     * 图片上传
     * @param  BackEndApiMainController $contll
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
        //上传头像不记录清除日期
        if ($folderName == self::USER_PUBLIC_AVATARS) {
            $newPubliData = ['pic_path' => $pic['path']];
            UserPublicAvatar::create($newPubliData);
            return $contll->msgOut(true, $pic);
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
