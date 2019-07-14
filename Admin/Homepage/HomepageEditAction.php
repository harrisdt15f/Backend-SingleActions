<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 16:57:50
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:18:45
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomepageEditAction
{
    protected $model;

    /**
     * @param  FrontendAllocatedModel  $frontendAllocatedModel
     */
    public function __construct(FrontendAllocatedModel $frontendAllocatedModel)
    {
        $this->model = $frontendAllocatedModel;
    }

    /**
     * 编辑首页模块
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastData = $this->model::find($inputDatas['id']);
        if (isset($inputDatas['status'])) {
            $pastData->status = $inputDatas['status'];
        }
        if (isset($inputDatas['value'])) {
            $pastData->value = $inputDatas['value'];
        }
        if (isset($inputDatas['show_num'])) {
            $pastData->show_num = $inputDatas['show_num'];
        }
        try {
            $pastData->save();
            //如果修改了展示状态  清楚首页展示model的缓存
            if (isset($inputDatas['status'])) {
                if (Cache::has('showModel')) {
                    Cache::forget('showModel');
                }
            }
            //删除前台首页缓存
            $contll->deleteCache($pastData->key);
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
