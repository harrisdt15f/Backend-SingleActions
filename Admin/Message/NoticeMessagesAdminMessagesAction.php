<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:11:58
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:00:39
 */
namespace App\Http\SingleActions\Backend\Admin\Message;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Message\BackendSystemInternalMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NoticeMessagesAdminMessagesAction
{
    protected $model;

    /**
     * @param  BackendSystemInternalMessage  $backendSystemInternalMessage
     */
    public function __construct(BackendSystemInternalMessage $backendSystemInternalMessage)
    {
        $this->model = $backendSystemInternalMessage;
    }

    /**
     * 当前管理员的站内信息
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $messagesEloq = $this->model::where('receive_admin_id', $contll->partnerAdmin->id)->with('noticeMessage')->orderBy('created_at', 'desc')->get();
        $messages = [];
        foreach ($messagesEloq as $messageEloq) {
            $data = [
                'id' => $messageEloq->id,
                'status' => $messageEloq->status,
                'type' => $messageEloq->noticeMessage->type,
                'message' => $messageEloq->noticeMessage->message,
                'created_at' => $messageEloq->created_at,
            ];
            $messages[] = $data;
        }
        return $contll->msgOut(true, $messages);
    }
}
