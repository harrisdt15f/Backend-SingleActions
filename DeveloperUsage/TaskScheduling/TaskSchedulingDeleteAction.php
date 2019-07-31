<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\TaskScheduling;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\TaskScheduling\CronJob;
use Illuminate\Http\JsonResponse;
use Exception;

class TaskSchedulingDeleteAction
{
    protected $model;

    /**
     * @param  CronJob  $cronJob
     */
    public function __construct(CronJob $cronJob)
    {
        $this->model = $cronJob;
    }

    /**
     * 删除任务调度
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        try {
            $this->model::find($inputDatas['id'])->delete();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $errorCode, $msg);
        }
    }
}
