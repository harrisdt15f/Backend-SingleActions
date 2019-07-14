<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:21:58
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:00:56
 */
namespace App\Http\SingleActions\Backend\Admin\Message;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\InternalNoticeMessage;
use App\Models\Admin\BackendAdminUser;
use App\Models\Admin\Message\BackendSystemNoticeList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NoticeMessagesSendMessagesAction
{
    /**
     * 手动发送站内信息
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $adminsArr = BackendAdminUser::select('id', 'group_id')->whereIn('id', $inputDatas['admins_id'])->get()->toArray();
        DB::beginTransaction();
        try {
            $messageObj = new InternalNoticeMessage();
            $type = BackendSystemNoticeList::ARTIFICIAL;
            $message = $inputDatas['message'];
            $messageObj->insertMessage($type, $message, $adminsArr, $contll->partnerAdmin->id);
            DB::commit();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $this->msgOut(false, [], $sqlState, $msg);
        }
    }
}
