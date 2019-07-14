<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 14:28:42
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 14:30:19
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Exception;
use Illuminate\Http\JsonResponse;

class FrontendAllocatedModelAddAction
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
     * 添加前端模块
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        if ($inputDatas['pid'] != 0) {
            $checkParentLevel = $this->model::where('id', $inputDatas['pid'])->first();
            if ($checkParentLevel->level === 3) {
                return $contll->msgOut(false, [], '101603');
            }
        }
        try {
            $modelEloq = new $this->model;
            $modelEloq->fill($inputDatas);
            $modelEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
