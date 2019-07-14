<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-27 11:43:23
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 11:48:10
 */
namespace App\Http\SingleActions\Backend\Users\Fund;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\FundOperation;
use App\Models\Admin\Fund\BackendAdminRechargePocessAmount;
use App\Models\BackendAdminAuditFlowList;
use App\Models\User\Fund\BackendAdminRechargehumanLog;
use App\Models\User\UsersRechargeHistorie;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RechargeCheckAuditFailureAction
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
     * 审核驳回
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $rechargeLog = $this->model::find($inputDatas['id']);
        if ($rechargeLog->status !== 0) {
            return $contll->msgOut(false, [], '100900');
        }
        $adminFundData = BackendAdminRechargePocessAmount::where('admin_id', $rechargeLog->admin_id)->first();
        if ($adminFundData === null) {
            return $contll->msgOut(false, [], '100903');
        }
        $newFund = $adminFundData->fund + $rechargeLog->amount;
        DB::beginTransaction();
        try {
            // 修改 backend_admin_rechargehuman_logs 表 的审核状态
            $rechargeLogEdit = ['status' => $rechargeLog::AUDITFAILURE];
            $rechargeLog->fill($rechargeLogEdit);
            $rechargeLog->save();
            // 修改 users_recharge_histories 表 的审核状态
            $historyEloq = UsersRechargeHistorie::where('audit_flow_id', $rechargeLog->audit_flow_id)->first();
            $historyEdit = ['status' => $historyEloq::AUDITFAILURE];
            $historyEloq->fill($historyEdit);
            $historyEloq->save();
            //退还管理员人工充值额度
            $auditFlow = BackendAdminAuditFlowList::where('id', $rechargeLog->audit_flow_id)->first();
            $adminFundDataEdit = ['fund' => $newFund];
            $contll->auditFlowEdit($auditFlow, $contll->partnerAdmin, $inputDatas['auditor_note']);
            $adminFundData->fill($adminFundDataEdit);
            $adminFundData->save();
            //返还额度后  backend_admin_rechargehuman_logs 记录表
            $rechargeLogeloqM = new $this->model;
            $type = $rechargeLogeloqM::SYSTEM;
            $in_out = $rechargeLogeloqM::INCREMENT;
            $comment = '[充值审核失败额度返还]==>+' . $rechargeLog['amount'] . '|[目前额度]==>' . $newFund;
            $fundOperationObj = new FundOperation();
            $fundOperationObj->insertOperationDatas($rechargeLogeloqM, $type, $in_out, null, null, $auditFlow->admin_id, $auditFlow->admin_name, $rechargeLog->amount, $comment, null);
            //发送站内消息提醒管理员
            $contll->sendMessage($rechargeLog->admin_id, $contll->failureMessage);
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
