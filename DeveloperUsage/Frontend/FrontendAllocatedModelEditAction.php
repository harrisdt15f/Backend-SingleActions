<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 14:32:05
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 14:36:30
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Frontend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Frontend\FrontendAllocatedModel;
use Exception;
use Illuminate\Http\JsonResponse;

class FrontendAllocatedModelEditAction
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
     * 编辑前端模块
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $isExistLabel = $this->model::where('label', $inputDatas['label'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($isExistLabel === true) {
            return $contll->msgOut(false, [], '101600');
        }
        $isExistName = $this->model::where('en_name', $inputDatas['en_name'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($isExistName === true) {
            return $contll->msgOut(false, [], '101601');
        }
        $pastDataEloq = $this->model::find($inputDatas['id']);
        try {
            $pastDataEloq->label = $inputDatas['label'];
            $pastDataEloq->en_name = $inputDatas['en_name'];
            $pastDataEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
