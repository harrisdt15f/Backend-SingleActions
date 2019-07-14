<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 15:05:04
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:16:23
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendPageBanner;
use Illuminate\Http\JsonResponse;

class HomepageBannerDetailAction
{
    protected $model;

    /**
     * @param  FrontendPageBanner  $frontendPageBanner
     */
    public function __construct(FrontendPageBanner $frontendPageBanner)
    {
        $this->model = $frontendPageBanner;
    }

    /**
     * 首页轮播图列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $data = $this->model::orderBy('sort', 'asc')->get()->toArray();
        return $contll->msgOut(true, $data);
    }
}
