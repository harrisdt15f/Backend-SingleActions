<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAppRoute;
use Exception;
use Illuminate\Http\JsonResponse;

class FrontendAppRouteIsOpenAction
{
    protected $model;

    /**
     * @param  FrontendAppRoute  $frontendAppRoute
     */
    public function __construct(FrontendAppRoute $frontendAppRoute)
    {
        $this->model = $frontendAppRoute;
    }

    /**
     * 设置APP路由是否开放
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastData = $this->model::find($inputDatas['id']);
        try {
            $pastData->is_open = $inputDatas['is_open'];
            $pastData->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
