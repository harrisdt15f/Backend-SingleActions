<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-28 10:49:15
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-28 10:52:00
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Routes;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Backend\BackendAdminRoute;
use Exception;
use Illuminate\Http\JsonResponse;

class RouteIsOpenAction
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
     * 路由开放接口
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        try {
            $routeEloq = $this->model::find($inputDatas['id']);
            $routeEloq->is_open = $inputDatas['is_open'];
            $routeEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
