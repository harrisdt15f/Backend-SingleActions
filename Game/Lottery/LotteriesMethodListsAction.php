<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 16:04:21
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:39:24
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use App\Models\Game\Lottery\LotteryMethod;
use App\Models\Game\Lottery\LotterySerie;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class LotteriesMethodListsAction
{
    protected $model;

    /**
     * @param  LotteryList  $lotteryList
     */
    public function __construct(LotteryList $lotteryList)
    {
        $this->model = $lotteryList;
    }

    /**
     * 获取玩法结果。
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $method = [];
        $redisKey = 'play_method_list';
        if (Cache::has($redisKey)) {
            $method = Cache::get($redisKey);
        } else {
            $seriesEloq = LotterySerie::get();
            foreach ($seriesEloq as $seriesIthem) {
                $lottery = $seriesIthem->lotteries; //->where('status',1)
                $seriesId = $seriesIthem->series_name;
                foreach ($lottery as $litems) {
                    $lotteyArr = collect($litems->toArray())
                        ->only(['id', 'cn_name', 'status']);
//                    $methodEloq = $litems->gameMethods;
                    $currentLotteryId = $litems->en_name;
                    $temp[$seriesId][$currentLotteryId]['data'] = $lotteyArr;
                    $temp[$seriesId][$currentLotteryId]['child'] = [];
                    //#########################################################
                    $methodGrops = $litems->methodGroups;
                    foreach ($methodGrops as $mgItems) {
                        $curentMethodGroup = $mgItems->method_group;
                        $methodGroupBool = $mgItems->where('lottery_id', $currentLotteryId)->where('method_group', $curentMethodGroup)->where('status', 1)->exists();
                        $methodGroupstatus = $methodGroupBool ? LotteryMethod::OPEN : LotteryMethod::CLOSE;
                        //玩法组 data
                        $methodGroup = $this->methodData($currentLotteryId, $curentMethodGroup, $methodGroupstatus);
                        //$temp 插入玩法组data
                        $temp[$seriesId][$currentLotteryId]['child'][$curentMethodGroup]['data'] = $methodGroup;
                        $temp[$seriesId][$currentLotteryId]['child'][$curentMethodGroup]['child'] = [];
                        //#########################################################
                        $methodRows = $mgItems->methodRows->where('lottery_id', $currentLotteryId);
                        foreach ($methodRows as $mrItems) {
                            $currentMethodRow = $mrItems->method_row;
                            $methodRowBool = $mrItems->where('lottery_id', $currentLotteryId)->where('method_group', $curentMethodGroup)->where('method_row', $currentMethodRow)->where('status', 1)->exists();
                            $methodRowstatus = $methodRowBool ? LotteryMethod::OPEN : LotteryMethod::CLOSE;
                            //玩法行 data
                            $methodRow = $this->methodData($currentLotteryId, $curentMethodGroup, $methodRowstatus, $currentMethodRow);
                            //$temp 插入玩法行data
                            $temp[$seriesId][$currentLotteryId]['child'][$curentMethodGroup]['child'][$mrItems->method_row]['data'] = $methodRow;
                            //玩法data
                            //###########################################################################################
                            $methodData = LotteryMethod::where('lottery_id', $currentLotteryId)->where('method_group', $curentMethodGroup)->where('method_row', $currentMethodRow)->get();
                            // $methodData = $mrItems->methodDetails
                            //     ->where('method_group', $curentMethodGroup)
                            //     ->where('method_row', $currentMethodRow);
                            //$temp 插入玩法data
                            $temp[$seriesId][$currentLotteryId]['child'][$curentMethodGroup]['child'][$mrItems->method_row]['child'] = $methodData;
                        }
                    }
                }
                $method = array_merge($method, $temp);
            }
            $hourToStore = 24;
            $expiresAt = Carbon::now()->addHours($hourToStore);
            Cache::put($redisKey, $method, $expiresAt);
        }
        return $contll->msgOut(true, $method);
    }

    /**
     * 组装玩法组和玩法行data
     * @param  int $lotteryId   [彩种]
     * @param  int $methodGroup [玩法组]
     * @param  int $status      [开启状态]
     * @param  int $methodRow   [玩法行]
     * @return array  $dataArr
     */
    public function methodData($lotteryId, $methodGroup, $status, $methodRow = null): array
    {
        $dataArr = [
            'lottery_id' => $lotteryId,
            'method_group' => $methodGroup,
            'status' => $status, //玩法行下是否存在开启状态的玩法
        ];
        if ($methodRow !== null) {
            $dataArr['method_row'] = $methodRow;
        }
        return $dataArr;
    }

}
