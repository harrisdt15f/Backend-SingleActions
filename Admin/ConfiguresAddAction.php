<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 20:05:55
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:04:09
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\SystemConfiguration;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ConfiguresAddAction
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
     * 添加配置
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $addDatas = $inputDatas;
        $addDatas['pid'] = $contll->currentPlatformEloq->platform_id;
        $addDatas['add_admin_id'] = $contll->partnerAdmin->id;
        $addDatas['last_update_admin_id'] = $contll->partnerAdmin->id;
        $addDatas['status'] = 1;
        try {
            $configure = new $this->model();
            $configure->fill($addDatas);
            $configure->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
