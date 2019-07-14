<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 13:55:20
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 14:04:40
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Routes;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Backend\BackendAdminRoute;
use Illuminate\Http\JsonResponse;

class RouteDetailAction
{
    protected $model;

    /**
     * @param  BackendAdminRoute  $backendAdminRoute
     */
    public function __construct(BackendAdminRoute $backendAdminRoute)
    {
        $this->model = $backendAdminRoute;
    }

    /**
     * 路由列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = $this->model::with('menu')->get();
        return $contll->msgOut(true, $datas);
    }
}
