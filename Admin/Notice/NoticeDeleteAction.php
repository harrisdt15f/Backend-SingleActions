<?php

namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Notice\FrontendMessageNotice;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NoticeDeleteAction
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
     * 删除 公告|站内信
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        try {
            $noticesContentEloq = $this->model::find($inputDatas['id']);
            $picStr = $noticesContentEloq->pic_path;
            $noticesContentEloq->delete();
            FrontendMessageNotice::where('notices_content_id', $inputDatas['id'])->delete();
            DB::commit();
            if ($picStr !== null) {
                $picArr = explode('|', $picStr);
                $imageObj = new ImageArrange();
                $imageObj->deleteImgs($picArr);
            }
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $errorCode, $msg);
        }
    }
}
