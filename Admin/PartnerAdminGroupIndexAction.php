<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 10:35:43
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 10:43:13
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminAccessGroup;
use Illuminate\Http\JsonResponse;

class PartnerAdminGroupIndexAction
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
     * Display a listing of the resource.
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $data = $this->model::all()->toArray();
        return $contll->msgOut(true, $data);
    }
}
