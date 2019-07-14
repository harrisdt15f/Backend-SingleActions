<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 11:16:04
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 11:20:12
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminAccessGroup;
use Illuminate\Http\JsonResponse;

class PartnerAdminGroupSpecificGroupUsersAction
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
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $accessGroupEloq = $this->model::find($inputDatas['id']);
        if ($accessGroupEloq !== null) {
            $data = $accessGroupEloq->adminUsers->toArray();
            return $contll->msgOut(true, $data);
        } else {
            return $contll->msgOut(false, [], '100202');
        }
    }
}
