<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Jobs\Lottery\Encode\IssueEncoder;
use App\Models\Game\Lottery\LotteryIssue;
use Illuminate\Http\JsonResponse;

class LotteriesInputCodeAction
{
    /**
     * 奖期录号
     * @param   BackEndApiMainController  $contll
     * @param   $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $issueEloq = LotteryIssue::where([
            ['issue', $inputDatas['issue']],
            ['lottery_id', $inputDatas['lottery_id']],
        ])->first();
        if ($issueEloq === null) {
            return $contll->msgOut(false, [], '101703');
        }
        if ($issueEloq->official_code !== null) {
            return $contll->msgOut(false, [], '101704');
        }
        $status_encode = LotteryIssue::ENCODED;
        $issueEloq->status_encode = $status_encode;
        $issueEloq->encode_time = time();
        $issueEloq->official_code = $inputDatas['code'];
        $issueEloq->encode_id = $contll->partnerAdmin->id;
        $issueEloq->encode_name = $contll->partnerAdmin->name;
        $issueEloq->save();
        if ($issueEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '', $issueEloq->errors()->messages());
        }
        if (!empty($issueEloq->toArray())) {
            dispatch(new IssueEncoder($issueEloq->toArray()))->onQueue('open_numbers');
        }
        return $contll->msgOut(true);
    }
}
