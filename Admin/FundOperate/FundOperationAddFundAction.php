<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 14:02:21
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:00:24
 */
namespace App\Http\SingleActions\Backend\Admin\FundOperate;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\FundOperation;
use App\Models\Admin\BackendAdminUser;
use App\Models\Admin\Fund\BackendAdminRechargePocessAmount;
use App\Models\User\Fund\BackendAdminRechargehumanLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FundOperationAddFundAction
{
    /**
     * 给管理员添加人工充值额度
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $adminDataEloq = BackendAdminUser::find($inputDatas['id']);
        $fundOperationAdmin = BackendAdminRechargePocessAmount::where('admin_id', $inputDatas['id'])->first();
        if (is_null($fundOperationAdmin)) {
            return $contll->msgOut(false, [], '101300');
        }
        //查看该管理员今日 手动添加的充值额度
        $fundOperationObj = new FundOperation();
        $checkFund = $fundOperationObj->checkRechargeToday($inputDatas['id'], $inputDatas['fund']);
        if ($checkFund['success'] === false) {
            return $contll->msgOut(false, [], '', $checkFund['msg']);
        }
        DB::beginTransaction();
        try {
            $newFund = $fundOperationAdmin->fund + $inputDatas['fund'];
            $adminEditData = ['fund' => $newFund];
            $fundOperationAdmin->fill($adminEditData);
            $fundOperationAdmin->save();
            $type = BackendAdminRechargehumanLog::SUPERADMIN;
            $in_out = BackendAdminRechargehumanLog::INCREMENT;
            $rechargeLog = new BackendAdminRechargehumanLog();
            $comment = '[人工充值额度操作]==>+' . $inputDatas['fund'] . '|[目前额度]==>' . $newFund;
            $fundOperationObj->insertOperationDatas($rechargeLog, $type, $in_out, $contll->partnerAdmin->id, $contll->partnerAdmin->name, $adminDataEloq->id, $adminDataEloq->name, $inputDatas['fund'], $comment, null);
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
