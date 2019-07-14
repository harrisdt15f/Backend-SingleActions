<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 15:18:22
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:35:18
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendWebRoute;
use Exception;
use Illuminate\Http\JsonResponse;

class FrontendWebRouteIsOpenAction
{
    protected $model;

    /**
     * @param  FrontendWebRoute  $frontendWebRoute
     */
    public function __construct(FrontendWebRoute $frontendWebRoute)
    {
        $this->model = $frontendWebRoute;
    }

    /**
     * 设置web路由是否开放
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        try {
            $pastDataEloq = $this->model::find($inputDatas['id']);
            $pastDataEloq->is_open = $inputDatas['is_open'];
            $pastDataEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
