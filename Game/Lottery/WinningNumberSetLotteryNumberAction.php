<?php
/**
 * Created by PhpStorm.
 * author: Harris
 * Date: 8/3/2019
 * Time: 1:41 PM
 */

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class WinningNumberSetLotteryNumberAction
{
    public function execute(BackEndApiMainController $contll, $inputDatas = [], $headers = []): JsonResponse
    {
        Log::channel('open-center')->info('Inputs are '.json_encode($inputDatas,JSON_PRETTY_PRINT));
        Log::channel('open-center')->info('Headers are '.json_encode($headers,JSON_PRETTY_PRINT));
        return $contll->msgOut(true);
    }
}