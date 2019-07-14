<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 14:20:51
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 14:26:54
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Illuminate\Http\JsonResponse;

class FrontendAllocatedModelDetailAction
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
     * 前端模块列表
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $eloqM = new $this->model;
        $allFrontendModel = $eloqM->allFrontendModel($inputDatas['type']);
        return $contll->msgOut(true, $allFrontendModel);
    }
}
