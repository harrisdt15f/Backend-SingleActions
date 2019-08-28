<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
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
     * @param  array                     $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, array $inputDatas): JsonResponse
    {
        $data = $this->model::where('flag', $inputDatas['flag'])->orderBy('sort', 'asc')->get()->toArray();
        return $contll->msgOut(true, $data);
    }
}
