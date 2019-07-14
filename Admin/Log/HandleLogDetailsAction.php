<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 18:55:21
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:20:41
 */
namespace App\Http\SingleActions\Backend\Admin\Log;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendSystemLog;
use Illuminate\Http\JsonResponse;

class HandleLogDetailsAction
{
    protected $model;

    /**
     * @param  BackendSystemLog  $BackendSystemLog
     */
    public function __construct(BackendSystemLog $backendSystemLog)
    {
        $this->model = $backendSystemLog;
    }
    /**
     * 后台日志列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $searchAbleFields = ['origin', 'ip', 'device', 'os', 'os_version', 'browser', 'admin_name', 'menu_label', 'device_type'];
        $data = $contll->generateSearchQuery($this->model, $searchAbleFields);
        return $contll->msgOut(true, $data);
    }
}
