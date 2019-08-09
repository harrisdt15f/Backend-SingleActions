<?php

namespace App\Http\SingleActions\Backend\DeveloperUsage\TaskScheduling;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\TaskScheduling\CronJob;
use Illuminate\Http\JsonResponse;

class TaskSchedulingAddAction
{
    /**
     * 添加任务调度
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $data = CronJob::insertCronJob($inputDatas);
        if ($data['success'] === false) {
            return $contll->msgOut(false, [], '400', $data['message']);
        }
        return $contll->msgOut(true);
    }
}
