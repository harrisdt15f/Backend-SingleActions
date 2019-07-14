<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 21:00:25
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:22:44
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\SystemConfiguration;
use Illuminate\Http\JsonResponse;

class ConfiguresGetSysConfigureValueAction
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
     * 获取某个配置的值
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $sysConfiguresEloq = new $this->model;
        $time = $sysConfiguresEloq->getConfigValue($inputDatas['key']);
        return $contll->msgOut(true, $time);
    }
}
