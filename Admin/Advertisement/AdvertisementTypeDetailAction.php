<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 15:05:42
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:12:48
 */
namespace App\Http\SingleActions\Backend\Admin\Advertisement;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Advertisement\FrontendSystemAdsType;
use Illuminate\Http\JsonResponse;

class AdvertisementTypeDetailAction
{
    protected $model;

    /**
     * @param  FrontendSystemAdsType  $frontendSystemAdsType
     */
    public function __construct(FrontendSystemAdsType $frontendSystemAdsType)
    {
        $this->model = $frontendSystemAdsType;
    }

    /**
     * 广告列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = $this->model::select('id', 'name', 'type', 'status', 'ext_type', 'l_size', 'w_size', 'size')->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
