<?php

namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\CacheRelated;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use Illuminate\Http\JsonResponse;

class NoticeEditAction
{
    protected $model;

    /**
     * @param  FrontendMessageNoticesContent  $frontendMessageNoticesContent
     */
    public function __construct(FrontendMessageNoticesContent $frontendMessageNoticesContent)
    {
        $this->model = $frontendMessageNoticesContent;
    }

    /**
     * 编辑 公告|站内信
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $noticesContentEloq = $this->model::find($inputDatas['id']);
        $editData = $inputDatas;
        unset($editData['pic_name']);
        if (isset($editData['pic_path']) && $editData['pic_path'] !== $noticesContentEloq->pic_path) {
            $pastPic = $noticesContentEloq->pic_path;
        }
        $contll->editAssignment($noticesContentEloq, $editData);
        $noticesContentEloq->save();
        if ($noticesContentEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $noticesContentEloq->errors()->messages());
        }
        if (isset($inputDatas['pic_name'])) {
            CacheRelated::deleteCachePic($inputDatas['pic_name'], '|'); //从定时清理的缓存图片中移除上传成功的图片
        }
        if (isset($pastPic)) {
            $pastPicArr = explode('|', $pastPic);
            $imageObj = new ImageArrange();
            $imageObj->deleteImgs($pastPicArr);
        }
        return $contll->msgOut(true);
    }
}
