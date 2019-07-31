<?php

namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\CacheRelated;
use App\Models\Admin\Notice\FrontendMessageNotice;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use App\Models\User\FrontendUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NoticeAddAction
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
     * 添加 公告|站内信
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $noticesContentData = $inputDatas;
        $noticesContentData['operate_admin_id'] = $contll->partnerAdmin->id;
        $noticesContentData['operate_admin_name'] = $contll->partnerAdmin->name;
        unset($noticesContentData['pic_name']);
        DB::beginTransaction();
        $noticesContentELoq = new $this->model;
        $noticesContentELoq->fill($noticesContentData);
        $noticesContentELoq->save();
        if ($noticesContentELoq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $noticesContentELoq->errors()->messages());
        }
        $userIds = FrontendUser::getAllUserIds(); //所有用户id Eloq
        foreach ($userIds as $user) {
            $messageNoticeData = [
                'receive_user_id' => $user->id,
                'notices_content_id' => $noticesContentELoq->id,
                'status' => FrontendMessageNotice::UN_READ,
            ];
            $messageNoticeEloq = new FrontendMessageNotice();
            $messageNoticeEloq->fill($messageNoticeData);
            $messageNoticeEloq->save();
            if ($messageNoticeEloq->errors()->messages()) {
                DB::rollback();
                return $contll->msgOut(false, [], '400', $messageNoticeEloq->errors()->messages());
            }
        }
        DB::commit();
        if (isset($inputDatas['pic_name'])) {
            CacheRelated::deleteCachePic($inputDatas['pic_name'], '|'); //从定时清理的缓存图片中移除上传成功的图片
        }
        return $contll->msgOut(true);
    }
}
