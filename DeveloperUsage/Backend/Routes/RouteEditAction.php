<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 14:09:56
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 14:22:09
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Routes;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Backend\BackendAdminRoute;
use Exception;
use Illuminate\Http\JsonResponse;

class RouteEditAction
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
     * 编辑路由
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $checkTitle = $this->model::where('title', $inputDatas['title'])->where('id', '!=', $inputDatas['id'])->first();
        if ($checkTitle !== null) {
            return $contll->msgOut(false, [], '101400');
        }
        $pastEloq = $this->model::find($inputDatas['id']);
        try {
            $contll->editAssignment($pastEloq, $inputDatas);
            $pastEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
