<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-27 11:35:18
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 11:48:13
 */
namespace App\Http\SingleActions\Backend\Users\Fund;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\AccountChange;
use App\Models\BackendAdminAuditFlowList;
use App\Models\User\FrontendUser;
use App\Models\User\Fund\BackendAdminRechargehumanLog;
use App\Models\User\Fund\FrontendUsersAccount;
use App\Models\User\Fund\FrontendUsersAccountsReport;
use App\Models\User\Fund\FrontendUsersAccountsType;
use App\Models\User\UsersRechargeHistorie;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RechargeCheckAuditSuccessAction
{
    protected $model;

    /**
     * @param  BackendAdminRechargehumanLog  $backendAdminRechargehumanLog
     */
    public function __construct(BackendAdminRechargehumanLog $backendAdminRechargehumanLog)
    {
        $this->model = $backendAdminRechargehumanLog;
    }

    /**
     * 审核通过
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        // 审核表
        $rechargeLog = $this->model::find($inputDatas['id']);
        if ($rechargeLog->status !== 0) {
            return $contll->msgOut(false, [], '100900');
        }
        $auditFlow = BackendAdminAuditFlowList::where('id', $rechargeLog->audit_flow_id)->first();
        if ($auditFlow === null) {
            return $contll->msgOut(false, [], '100904');
        }
        //检查是否存在 人工充值 的帐变类型表
        $accountChangeTypeEloq = FrontendUsersAccountsType::where('sign', 'artificial_recharge')->first();
        if (is_null($accountChangeTypeEloq)) {
            return $contll->msgOut(false, [], '100901');
        }
        DB::beginTransaction();
        try {
            // 修改 backend_admin_rechargehuman_logs 表 的审核状态
            $rechargeLogEdit = ['status' => $rechargeLog::AUDITSUCCESS];
            $rechargeLog->fill($rechargeLogEdit);
            $rechargeLog->save();
            // 修改 users_recharge_histories 表 的审核状态
            $historyEloq = UsersRechargeHistorie::where('audit_flow_id', $rechargeLog->audit_flow_id)->first();
            $historyEdit = ['status' => $historyEloq::AUDITSUCCESS];
            $historyEloq->fill($historyEdit);
            $historyEloq->save();
            //修改backend_admin_audit_flow_lists审核表
            $userData = FrontendUser::where('id', $rechargeLog->user_id)->with('account')->first();
            $balance = $userData->account->balance + $rechargeLog->amount;
            $contll->auditFlowEdit($auditFlow, $contll->partnerAdmin, $inputDatas['auditor_note']);
            //修改用户金额
            $UserAccounts = FrontendUsersAccount::where('user_id', $rechargeLog->user_id)->first();
            $UserAccountsEdit = ['balance' => $balance];
            $editStatus = FrontendUsersAccount::where('user_id', $UserAccounts->user_id)->where('updated_at', $UserAccounts->updated_at)->update($UserAccountsEdit);
            if ($editStatus === 0) {
                DB::rollBack();
                return $contll->msgOut(false, [], '100902');
            }
            //用户帐变表
            $accountChangeReportEloq = new FrontendUsersAccountsReport();
            $accountChangeObj = new AccountChange();
            $accountChangeObj->addData($accountChangeReportEloq, $userData, $rechargeLog['amount'], $UserAccounts->balance, $balance, $accountChangeTypeEloq);
            //发送站内消息提醒管理员
            $contll->sendMessage($rechargeLog->admin_id, $contll->successMessage);
            DB::commit();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollBack();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
