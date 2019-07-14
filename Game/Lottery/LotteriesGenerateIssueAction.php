<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 17:14:58
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-25 10:53:00
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Events\IssueGenerateEvent;
use App\Http\Controllers\backendApi\BackEndApiMainController;
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
