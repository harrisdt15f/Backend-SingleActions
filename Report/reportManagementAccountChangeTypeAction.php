<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 18:12:25
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 18:13:47
 */
namespace App\Http\SingleActions\Backend\Report;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsType;
use Illuminate\Http\JsonResponse;

class reportManagementAccountChangeTypeAction
{
    /**
     * 帐变类型列表
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = FrontendUsersAccountsType::select('name', 'sign')->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
