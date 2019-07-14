<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 14:42:45
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 14:55:36
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAppRoute;
use Illuminate\Http\JsonResponse;

class FrontendAppRouteDetailAction
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
     * APP路由列表
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = $this->model::select('id', 'route_name', 'frontend_model_id', 'title', 'description', 'is_open')->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
