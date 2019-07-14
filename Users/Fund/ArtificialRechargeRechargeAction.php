<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-27 10:54:56
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 11:23:40
 */
namespace App\Http\SingleActions\Backend\Users\Fund;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\AccountChange;
use App\Lib\Common\FundOperation;
use App\Lib\Common\InternalNoticeMessage;
use App\Models\Admin\BackendAdminAccessGroup;
use App\Models\Admin\BackendAdminUser;
use App\Models\Admin\Fund\BackendAdminRechargePocessAmount;
use App\Models\Admin\Message\BackendSystemNoticeList;
use App\Models\BackendAdminAuditFlowList;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use App\Models\User\FrontendUser;
use App\Models\User\Fund\BackendAdminRechargehumanLog;
use App\Models\User\Fund\FrontendUsersAccount;
use App\Models\User\Fund\FrontendUsersAccountsReport;
use App\Models\User\Fund\FrontendUsersAccountsType;
use App\Models\User\UsersRechargeHistorie;
use App\Models\User\UsersRechargeLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ArtificialRechargeRechargeAction
{
    /**
     * 给用户人工充值
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        $partnerAdmin = $contll->partnerAdmin;
        try {
            //普通管理员人工充值需要审核的操作
            if ($contll->currentPartnerAccessGroup->role !== '*') {
                //扣除管理员额度
                $adminFundData = BackendAdminRechargePocessAmount::where('admin_id', $partnerAdmin->id)->first();
                if ($adminFundData === null) {
                    return $contll->msgOut(false, [], '101100');
                }
                $adminOperationFund = $adminFundData->fund;
                //可操作额度小于充值额度
                if ($adminOperationFund < $inputDatas['amount']) {
                    return $contll->msgOut(false, [], '101101');
                }
                $newFund = $adminOperationFund - $inputDatas['amount'];
                $adminFundEdit = ['fund' => $newFund];
                $adminFundData->fill($adminFundEdit);
                $adminFundData->save();
                //插入审核表
                $auditFlowID = $this->insertAuditFlow($partnerAdmin->id, $partnerAdmin->name, $inputDatas['apply_note']);
                //发送站内消息 提醒有权限的管理员审核
                $this->sendMessage();
            } else {
                //超管操作不需审核 直接给用户充值
                $accountChangeTypeEloq = FrontendUsersAccountsType::where('sign', 'artificial_recharge')->first();
                if ($accountChangeTypeEloq === null) {
                    return $contll->msgOut(false, [], '100901');
                }
                //修改用户金额
                $UserAccounts = FrontendUsersAccount::where('user_id', $inputDatas['id'])->first();
                $balance = $UserAccounts->balance + $inputDatas['amount'];
                $UserAccountsEdit = ['balance' => $balance];
                $editStatus = FrontendUsersAccount::where('user_id', $UserAccounts->user_id)->where('updated_at', $UserAccounts->updated_at)->update($UserAccountsEdit);
                //充值失败回滚
                if ($editStatus === 0) {
                    DB::rollBack();
                    return $contll->msgOut(false, [], '101102');
                }
                //用户帐变表
                $accountChangeReportEloq = new FrontendUsersAccountsReport();
                $accountChangeObj = new AccountChange();
                $userEloq = FrontendUser::find($inputDatas['id']);
                $accountChangeObj->addData($accountChangeReportEloq, $userEloq->toArray(), $inputDatas['amount'], $UserAccounts->balance, $balance, $accountChangeTypeEloq);
            }
            //添加人工充值明细表
            $auditFlowID = $auditFlowID ?? null;
            $newFund = $newFund ?? null;
            $this->insertFundLog($partnerAdmin, $userEloq, $auditFlowID, $inputDatas['amount'], $newFund, $contll->currentPartnerAccessGroup->role);
            //用户 users_recharge_histories 表
            $deposit_mode = UsersRechargeHistorie::ARTIFICIAL;
            $companyOrderNum = $this->insertRechargeHistory($userEloq, $auditFlowID, $deposit_mode, $inputDatas['amount'], $contll->currentPartnerAccessGroup->role);
            // 用户 users_recharge_logs 表
            $this->insertRechargeLog($companyOrderNum, $deposit_mode, $contll->log_uuid);
            DB::commit();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollBack();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }

    /**
     * 插入审核表
     * @param  int    $admin_id
     * @param  string $admin_name
     * @param  string $apply_note [备注]
     * @return int
     */
    public function insertAuditFlow($admin_id, $admin_name, $apply_note): int
    {
        $insertData = [
            'admin_id' => $admin_id,
            'admin_name' => $admin_name,
            'apply_note' => $apply_note,
        ];
        $auditFlow = new BackendAdminAuditFlowList();
        $auditFlow->fill($insertData);
        $auditFlow->save();
        return $auditFlow->id;
    }

    /**
     * 发送站内消息 提醒有权限的管理员审核
     * @return void
     */
    public function sendMessage(): void
    {
        $messageObj = new InternalNoticeMessage();
        $type = BackendSystemNoticeList::AUDIT;
        $roleId = BackendSystemMenu::where('en_name', 'recharge.check')->value('id');
        $allGroup = BackendAdminAccessGroup::select('id', 'role')->get();
        $groupIds = [];
        //获取有人工充值权限的组
        foreach ($allGroup as $group) {
            if ($group->role === '*') {
                $groupIds[] = $group->id;
            } else {
                $roleArr = json_decode($group->role, true);
                if (array_key_exists($roleId, $roleArr)) {
                    $groupIds[] = $group->id;
                }
            }
        }
        //获取有人工充值权限的管理员
        $admins = BackendAdminUser::select('id', 'group_id')->whereIn('group_id', $groupIds)->get();
        if ($admins !== null) {
            $messageObj->insertMessage($type, $contll->message, $admins->toArray());
        }
    }

    /**
     * 插入充值额度记录
     * @param  object $partnerAdmin  [管理员eloq]
     * @param  object $userEloq      [用户eloq]
     * @param  int    $auditFlowID   [backend_admin_audit_flow_lists审核表id]
     * @param  int    $amount        [变动的额度]
     * @param  int    $newFund       [变动后的额度]
     * @param  string $role
     * @return void
     */
    public function insertFundLog($partnerAdmin, $userEloq, $auditFlowID, $amount, $newFund, $role): void
    {
        $rechargeLog = new BackendAdminRechargehumanLog();
        $type = $role !== '*' ? BackendAdminRechargehumanLog::ADMIN : 3;
        $in_out = BackendAdminRechargehumanLog::DECREMENT;
        $comment = '[给用户人工充值]==>-' . $amount . '|[目前额度]==>' . $newFund;
        $fundOperationObj = new FundOperation();
        $fundOperationObj->insertOperationDatas($rechargeLog, $type, $in_out, $partnerAdmin->id, $partnerAdmin->name, $userEloq->id, $userEloq->username, $amount, $comment, $auditFlowID);
    }

    /**
     * 插入users_recharge_histories表
     * @param  objact  $userEloq       [用户eloq]
     * @param  int     $auditFlowID    [backend_admin_audit_flow_lists审核表id]
     * @param  int     $deposit_mode   [充值模式 0自动 1手动]
     * @param  int     $amount         [金额]
     * @param  string  $role
     * @return string
     */
    public function insertRechargeHistory($userEloq, $auditFlowID, $deposit_mode, $amount, $role)
    {
        $userRechargeHistory = new UsersRechargeHistorie();
        $status = $role !== '*' ? UsersRechargeHistorie::UNDERWAYAUDIT : UsersRechargeHistorie::AUDITSUCCESS;
        $rechargeHistoryArr = $this->insertRechargeHistoryArr($userEloq->id, $userEloq->username, $userEloq->is_tester, $userEloq->top_id, $amount, $auditFlowID, $status, $deposit_mode);
        $userRechargeHistory->fill($rechargeHistoryArr);
        $userRechargeHistory->save();
        return $userRechargeHistory->company_order_num;
    }

    /**
     * 插入users_recharge_logs表
     * @param  string   $companyOrderNum   [充值订单号]
     * @param  int      $deposit_mode      [充值模式 0自动 1手动]
     * @param  string   $log_uuid
     * @return void
     */
    public function insertRechargeLog($companyOrderNum, $deposit_mode, $log_uuid): void
    {
        $rchargeLogeEloq = new UsersRechargeLog();
        $log_num = $log_uuid;
        $rechargeLogArr = $this->insertRechargeLogArr($companyOrderNum, $log_num, $deposit_mode);
        $rchargeLogeEloq->fill($rechargeLogArr);
        $rchargeLogeEloq->save();
    }

    /**
     * 插入users_recharge_histories    人工充值 $deposit_mode=1 后面不需要在传参
     * @param  int    $user_id
     * @param  string $user_name
     * @param  int    $is_tester
     * @param  int    $top_agent
     * @param  float  $amount
     * @param  int    $audit_flow_id
     * @param  int    $status
     * @param  int    $deposit_mode
     * @param  int    $channel
     * @param  int    $payment_id      [支付通道id]
     * @param  float  $real_amount     [实际支付金额]
     * @param  float  $fee             [手续费]
     * @return array
     */
    public function insertRechargeHistoryArr($user_id, $user_name, $is_tester, $top_agent, $amount, $audit_flow_id, $status, $deposit_mode, $channel = null, $payment_id = null, $real_amount = null, $fee = null): array
    {
        $insertSqlArr = [
            'user_id' => $user_id,
            'user_name' => $user_name,
            'is_tester' => $is_tester,
            'top_agent' => $top_agent,
            'deposit_mode' => $deposit_mode,
            'company_order_num' => $this->createOrder(),
            'amount' => $amount,
            'audit_flow_id' => $audit_flow_id,
            'status' => $status,
        ];
        if ($deposit_mode === 0) {
            $insertDataArr = [
                'channel' => $channel,
                'payment_id' => $payment_id,
                'real_amount' => $real_amount,
                'fee' => $fee,
            ];
            $insertSqlArr = array_merge($insertSqlArr, $insertDataArr);
        }
        return $insertSqlArr;
    }

    /**
     * 生成充值订单号
     * @return string
     */
    public function createOrder(): string
    {
        return date('Ymd') . substr(time(), -4) . random_int(100000, 999999) . substr(uniqid(time(), true), -7);
    }

    /**
     * 插入users_recharge_logs     人工充值 $deposit_mode=1 后面不需要在传参
     * @param  string $company_order_num
     * @param  string $log_num
     * @param  int    $deposit_mode
     * @param  string $req_type_1_params
     * @param  string $req_type_2_params
     * @param  string $req_type_4_params
     * @param  int    $req_type
     * @param  float  $real_amount
     * @return array
     */
    public function insertRechargeLogArr($company_order_num, $log_num, $deposit_mode, $req_type_1_params = null, $req_type_2_params = null, $req_type_4_params = null, $req_type = null, $real_amount = null): array
    {
        $insertSqlArr = [
            'company_order_num' => $company_order_num,
            'log_num' => $log_num,
            'deposit_mode' => $deposit_mode,
        ];
        if ($deposit_mode === 0) {
            $insertDataArr = [
                'req_type_1_params' => $req_type_1_params,
                'req_type_2_params' => $req_type_2_params,
                'req_type_4_params' => $req_type_4_params,
                'req_type' => $req_type,
                'real_amount' => $real_amount,
            ];
            $insertSqlArr = array_merge($insertSqlArr, $insertDataArr);
        }
        return $insertSqlArr;
    }
}
