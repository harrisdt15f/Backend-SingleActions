<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 15:13:47
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:12:56
 */
namespace App\Http\SingleActions\Backend\Admin\Advertisement;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Advertisement\FrontendSystemAdsType;
use Exception;
use Illuminate\Http\JsonResponse;

class AdvertisementTypeEditAction
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
     * 编辑广告类型
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $editData = $this->model::find($inputDatas['id']);
        $contll->editAssignment($editData, $inputDatas);
        try {
            $editData->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
