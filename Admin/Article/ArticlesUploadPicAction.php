<?php

namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Lib\Common\CacheRelated;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Activity\BackendAdminMessageArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

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
        $depositPath = $imageObj->depositPath(
            $folderName,
            $contll->currentPlatformEloq->platform_id,
            $contll->currentPlatformEloq->platform_name
        );
        //进行上传
        $pic = $imageObj->uploadImg($file, $depositPath);
        if ($pic['success'] === false) {
            return $contll->msgOut(false, [], '400', $pic['msg']);
        }
        //设置图片过期时间6小时
        $pic['expire_time'] = Carbon::now()->addHours(6)->timestamp;
        $tags = 'images';
        $redisKey = 'cleaned_images';
        $cachePic = CacheRelated::getTagsCache($tags, $redisKey);
        $cachePic[$pic['name']] = $pic;
        $minuteToStore = 60 * 24 * 2;
        CacheRelated::setTagsCache($tags, $redisKey, $cachePic, $minuteToStore);
        return $contll->msgOut(true, $pic);
    }
}
