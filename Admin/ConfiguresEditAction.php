<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 20:12:27
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:22:15
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\SystemConfiguration;
use Exception;
use Illuminate\Http\JsonResponse;

class ConfiguresEditAction
{
    protected $model;

    /**
     * @param  SystemConfiguration  $systemConfiguration
     */
    public function __construct(SystemConfiguration $systemConfiguration)
    {
        $this->model = $systemConfiguration;
    }

    /**
     * 修改配置
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $checkSign = $this->model::where('sign', $inputDatas['sign'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($checkSign === true) {
            return $contll->msgOut(false, [], '100700');
        }
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $contll->editAssignment($pastDataEloq, $inputDatas);
        try {
            $pastDataEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
