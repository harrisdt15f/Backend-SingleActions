<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 16:15:40
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:03:53
 */
namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\BackendAdminMessageArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ArticlesUploadPicAction
{
    protected $model;

    /**
     * @param  BackendAdminMessageArticle  $backendAdminMessageArticle
     */
    public function __construct(BackendAdminMessageArticle $backendAdminMessageArticle)
    {
        $this->model = $backendAdminMessageArticle;
    }

    /**
     * 图片上传
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $imageObj = new ImageArrange();
        $file = $inputDatas['pic'];
        $folderName = $inputDatas['folder_name'];
        $depositPath = $imageObj->depositPath($folderName, $contll->currentPlatformEloq->platform_id, $contll->currentPlatformEloq->platform_name);
        //进行上传
        $pic = $imageObj->uploadImg($file, $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        //设置图片过期时间6小时
        $pic['expire_time'] = Carbon::now()->addHours(6)->timestamp;
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
