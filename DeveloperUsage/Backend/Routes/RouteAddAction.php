<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Routes;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Backend\BackendAdminRoute;
use Exception;
use Illuminate\Http\JsonResponse;

class RouteAddAction
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
     * 添加路由
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        try {
            $routeEloq = new $this->model;
            $routeEloq->fill($inputDatas);
            $routeEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
