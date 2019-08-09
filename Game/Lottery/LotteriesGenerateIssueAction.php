<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Events\IssueGenerateEvent;
use App\Http\Controllers\BackendApi\BackEndApiMainController;
use Illuminate\Http\JsonResponse;

class LotteriesGenerateIssueAction
{
    /**
     * 生成奖期
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        event(new IssueGenerateEvent($inputDatas));
        return $contll->msgOut(true);
    }
}
