<?php

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
