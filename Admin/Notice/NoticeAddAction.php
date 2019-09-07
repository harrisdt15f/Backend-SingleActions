<?php

namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use App\Models\Admin\Notice\FrontendMessageNoticesContent;
use App\Models\User\FrontendUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Lib\BaseCache;

class NoticeAddAction
{
    use BaseCache;

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
     * @param  array                     $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, array $inputDatas): JsonResponse
    {
        $noticesContentData = $inputDatas;
        $noticesContentData['operate_admin_id'] = $contll->partnerAdmin->id;
        $noticesContentData['operate_admin_name'] = $contll->partnerAdmin->name;
        unset($noticesContentData['pic_name']);
        //发布公告需要获取sort
        if ((int) $inputDatas['type'] === $this->model::TYPE_NOTICE) {
            $maxSort = $this->model::select('sort')->max('sort');
            $noticesContentData['sort'] = ++$maxSort;
        }
        DB::beginTransaction();
        $noticesContentELoq = $this->model;
        $noticesContentELoq->fill($noticesContentData);
        $noticesContentELoq->save();
        if ($noticesContentELoq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '', $noticesContentELoq->errors()->messages());
        }
        //发布站内信需要添加关联用户的frontend_message_notices表
        if ((int) $inputDatas['type'] === $this->model::TYPE_MESSAGE) {
            $insertUserNotice = $this->insertUserNotice($noticesContentELoq->id);
            if ($insertUserNotice !== true) {
                return $contll->msgOut(false, [], '', $insertUserNotice);
            }
        }
        DB::commit();
        if (isset($inputDatas['pic_name'])) {
            self::deleteCachePic($inputDatas['pic_name'], '|'); //从定时清理的缓存图片中移除上传成功的图片
        }
        return $contll->msgOut(true);
    }

    /**
     * 给用户发送站内信
     * @param  int $noticesContentId
     */
    public function insertUserNotice($noticesContentId)
    {
        $userIds = FrontendUser::getAllUserIds(); //所有用户id Eloq
        foreach ($userIds as $user) {
            $messageNoticeData = [
                'receive_user_id' => $user->id,
                'notices_content_id' => $noticesContentId,
                'status' => FrontendMessageNotice::STATUS_UNREAD,
            ];
            $messageNoticeEloq = new FrontendMessageNotice();
            $messageNoticeEloq->fill($messageNoticeData);
            $messageNoticeEloq->save();
            if ($messageNoticeEloq->errors()->messages()) {
                DB::rollback();
                return $messageNoticeEloq->errors()->messages();
            }
        }
        return true;
    }
}
