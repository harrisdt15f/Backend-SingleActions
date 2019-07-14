<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 16:30:44
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:18:18
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Advertisement\FrontendSystemAdsType;
use Illuminate\Http\JsonResponse;

class HomepageBannerPicStandardAction
{
    /**
     * 上传图片的规格
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $standard = FrontendSystemAdsType::select('l_size', 'w_size', 'size')->where('type', 1)->first()->toArray();
        return $contll->msgOut(true, $standard);
    }
}
