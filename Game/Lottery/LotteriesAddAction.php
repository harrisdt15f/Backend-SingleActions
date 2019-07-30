<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\CacheRelated;
use App\Models\Game\Lottery\CronJob;
use App\Models\Game\Lottery\LotteryIssueRule;
use App\Models\Game\Lottery\LotteryList;
use App\Models\Game\Lottery\LotteryMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LotteriesAddAction
{
    /**
     * 添加彩种
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        $lotteryDatas = $inputDatas['lottery'];
        unset($lotteryDatas['icon_name']);
        $lotteryEloq = new LotteryList();
        $lotteryEloq->fill($lotteryDatas);
        $lotteryEloq->save();
        if ($lotteryEloq->errors()->messages()) {
            DB::rollback();
            $imageObj->deletePic($icon['path']);
            return $contll->msgOut(false, [], '400', $lotteryEloq->errors()->messages());
        }
        $methodELoq = new LotteryMethod();
        $insertStatus = $methodELoq->cloneLotteryMethods($lotteryEloq); //克隆彩种玩法
        if ($insertStatus['success'] === false) {
            DB::rollback();
            $imageObj->deletePic($icon['path']);
            return $contll->msgOut(false, [], '400', $insertStatus['message']);
        }
        if ($inputDatas['lottery']['auto_open'] == 1) {
            $createData = CronJob::createCronJob($inputDatas['cron']); //自开彩种 自动开奖任务
            if ($createData['success'] === false) {
                DB::rollback();
                $imageObj->deletePic($icon['path']);
                return $contll->msgOut(false, [], '400', $createData['message']);
            }
        }
        $issueRuleELoq = new LotteryIssueRule();
        $issueRuleELoq->fill($inputDatas['issue_rule']);
        $issueRuleELoq->save();
        if ($issueRuleELoq->errors()->messages()) {
            DB::rollback();
            $imageObj->deletePic($icon['path']);
            return $contll->msgOut(false, [], '400', $issueRuleELoq->errors()->messages());
        }
        DB::commit();
        CacheRelated::deleteCachePic($inputDatas['lottery']['icon_name']); //从定时清理的缓存图片中移除上传成功的图片
        $lotteryEloq->lotteryInfoCache(); //更新首页lotteryInfo缓存
        return $contll->msgOut(true);
    }
}
