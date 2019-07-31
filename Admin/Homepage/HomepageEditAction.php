<?php

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
        $pastData->save();
        if ($pastData->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $pastData->errors()->messages());
        }
        //如果修改了展示状态  更新首页展示model的缓存
        if (isset($inputDatas['status'])) {
            $this->model::showModelCache();
        }
        //删除前台首页缓存
        $contll->deleteCache($pastData->key);
        return $contll->msgOut(true);
    }
}
