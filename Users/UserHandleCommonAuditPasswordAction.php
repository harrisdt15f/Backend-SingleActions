<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-27 18:30:56
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 18:37:07
 */
namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\FrontendUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserHandleCommonAuditPasswordAction
{
    /**
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $eloqM = $contll->modelWithNameSpace($contll->withNameSpace);
        $applyUserEloq = $eloqM::where([
            ['id', '=', $inputDatas['id']],
            ['type', '=', $inputDatas['type']],
            ['status', '=', 0],
        ])->first();
        if ($applyUserEloq !== null) {
            $auditFlowEloq = $applyUserEloq->auditFlow;
            //handle User
            $user = FrontendUser::find($applyUserEloq->user_id);
            if ($applyUserEloq->type == 1) {
                $user->password = $applyUserEloq->audit_data;
            } else {
                $user->fund_password = $applyUserEloq->audit_data;
            }
            DB::beginTransaction();
            try {
                if ($inputDatas['status'] == 1) {
                    $user->save();
                }
                $auditFlowEloq->auditor_id = $contll->partnerAdmin->id;
                $auditFlowEloq->auditor_note = $inputDatas['auditor_note'];
                $auditFlowEloq->auditor_name = $contll->partnerAdmin->name;
                $auditFlowEloq->save();
                $applyUserEloq->status = $inputDatas['status'];
                $applyUserEloq->save();
                DB::commit();
                return $contll->msgOut(true);
            } catch (Exception $e) {
                DB::rollBack();
                $errorObj = $e->getPrevious()->getPrevious();
                [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
                return $contll->msgOut(false, [], $sqlState, $msg);
            }
        } else {
            return $contll->msgOut(false, [], '100102');
        }
    }
}
