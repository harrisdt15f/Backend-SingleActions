<?php

namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use Illuminate\Http\JsonResponse;

class UserHandleCommonAppliedPasswordHandleAction
{
    /**
     * 用户登录密码和资金密码公用列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        //main model
        $eloqM = $contll->modelWithNameSpace($contll->withNameSpace);
        //target model to join
        $fixedJoin = 1; //number of joining tables
        $withTable = 'auditFlow:id,admin_id,auditor_id,apply_note,auditor_note,updated_at,admin_name,auditor_name,username';
        $searchAbleFields = ['type', 'status', 'created_at', 'updated_at'];
        $withSearchAbleFields = ['username'];
        $data = $contll->generateSearchQuery($eloqM, $searchAbleFields, $fixedJoin, $withTable, $withSearchAbleFields);
        return $contll->msgOut(true, $data);
    }
}
