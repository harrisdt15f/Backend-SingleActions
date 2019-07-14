<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 15:08:03
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 15:10:08
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendWebRoute;
use Illuminate\Http\JsonResponse;

class FrontendWebRouteDetailAction
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
     * web路由列表
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = $this->model::select('id', 'route_name', 'frontend_model_id', 'title', 'description', 'is_open')->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
