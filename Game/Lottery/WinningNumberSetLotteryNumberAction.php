<?php
/**
 * Created by PhpStorm.
 * author: Harris
 * Date: 8/3/2019
 * Time: 1:41 PM
 */

namespace App\Http\SingleActions\Backend\Game\Lottery;


use Illuminate\Support\Facades\Log;

class WinningNumberSetLotteryNumberAction
{
    public function execute(BackEndApiMainController $contll, $inputDatas = [], $headers = []): JsonResponse
    {
        Log::info(json_encode($inputDatas));
        Log::info(json_encode($headers));
        return $contll->msgOut(true);
    }
}