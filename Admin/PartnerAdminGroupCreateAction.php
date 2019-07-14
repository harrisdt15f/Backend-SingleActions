<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 10:43:34
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:05:41
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminAccessGroup;
use App\Models\Admin\Fund\BackendAdminRechargePermitGroup;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PartnerAdminGroupCreateAction
{
    protected $model;

    /**
     * @param  BackendAdminAccessGroup  $backendAdminAccessGroup
     */
    public function __construct(BackendAdminAccessGroup $backendAdminAccessGroup)
    {
        $this->model = $backendAdminAccessGroup;
    }

    /**
     * Show the form for creating a new resource.
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data['platform_id'] = $contll->currentPlatformEloq->platform_id;
            $data['group_name'] = $inputDatas['group_name'];
            $data['role'] = $inputDatas['role'];
            $role = $inputDatas['role'] == '*' ? Arr::wrap($inputDatas['role']) : Arr::wrap(json_decode($inputDatas['role'],
                true));
            $objPartnerAdminGroup = new $this->model;
            $objPartnerAdminGroup->fill($data);
            $objPartnerAdminGroup->save();
            //检查是否有人工充值权限
            $fundOperationCriteriaEloq = BackendSystemMenu::select('id')->where('route', '/manage/recharge')->first();
            $isManualRecharge = in_array($fundOperationCriteriaEloq['id'], $role);
            //如果有人工充值权限   添加 backend_admin_recharge_permit_groups 表
            if ($isManualRecharge === true) {
                $fundOperationGroup = new BackendAdminRechargePermitGroup();
                $fundOperationData = [
                    'group_id' => $objPartnerAdminGroup->id,
                    'group_name' => $objPartnerAdminGroup->group_name,
                ];
                $fundOperationGroup->fill($fundOperationData);
                $fundOperationGroup->save();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
        $partnerMenuObj = new BackendSystemMenu();
        $partnerMenuObj->createMenuDatas($objPartnerAdminGroup->id, $role);
        return $contll->msgOut(true, $data);
    }
}
