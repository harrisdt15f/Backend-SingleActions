<?php

namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotteryList;
use App\Models\Game\Lottery\LotteryMethod;
use App\Models\Game\Lottery\LotterySerie;
use Illuminate\Http\JsonResponse;
use App\Lib\BaseCache;

class LotteriesMethodListsAction
{
    use BaseCache;

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
        $redisKey = $contll->redisKey;
        $data = self::getTagsCacheData($redisKey);
        if (empty($data)) {
            $seriesEloq = LotterySerie::get();
            foreach ($seriesEloq as $seriesIthem) {
                $lottery = $seriesIthem->lotteries; //->where('status',1)
                $seriesId = $seriesIthem->series_name;
                foreach ($lottery as $litems) {
                    $lotteyArr = collect($litems->toArray())
                        ->only(['id', 'cn_name', 'status']);
//                    $methodEloq = $litems->gameMethods;
                    $currentLotteryId = $litems->en_name;
                    $method[$seriesId][$currentLotteryId]['data'] = $lotteyArr;
                    $method[$seriesId][$currentLotteryId]['child'] = [];
                    //#########################################################
                    $methodGrops = $litems->methodGroups;
                    foreach ($methodGrops as $mgItems) {
                        $curentMethodGroup = $mgItems->method_group;
                        $methodGroupBool = $mgItems->where('lottery_id', $currentLotteryId)
                            ->where('method_group', $curentMethodGroup)
                            ->where('status', 1)
                            ->exists();
                        $methodGroupstatus = $methodGroupBool ? LotteryMethod::OPEN : LotteryMethod::CLOSE;
                        //玩法组 data
                        $methodGroup = $this->methodData($currentLotteryId, $curentMethodGroup, $methodGroupstatus);
                        //$data 插入玩法组data
                        $data[$seriesId][$currentLotteryId]['child'][$curentMethodGroup]['data'] = $methodGroup;
                        $data[$seriesId][$currentLotteryId]['child'][$curentMethodGroup]['child'] = [];
                        //#########################################################
                        $methodRows = $mgItems->methodRows;
                        foreach ($methodRows as $mrItems) {
                            $currentMethodRow = $mrItems->method_row;
                            $methodRowBool = $mrItems->where('lottery_id', $currentLotteryId)
                                ->where('method_group', $curentMethodGroup)
                                ->where('method_row', $currentMethodRow)
                                ->where('status', 1)
                                ->exists();
                            $methodRowstatus = $methodRowBool ? LotteryMethod::OPEN : LotteryMethod::CLOSE;
                            //玩法行 data
                            $methodRow = $this->methodData(
                                $currentLotteryId,
                                $curentMethodGroup,
                                $methodRowstatus,
                                $currentMethodRow
                            );
                            //$data 插入玩法行data
                            $data[$seriesId][$currentLotteryId]['child']
                            [$curentMethodGroup]['child'][$mrItems->method_row]['data'] = $methodRow;
                            //玩法data
                            //###########################################################################################
                            $methodData = LotteryMethod::where('lottery_id', $currentLotteryId)
                                ->where('method_group', $curentMethodGroup)
                                ->where('method_row', $currentMethodRow)
                                ->get();
                            // $methodData = $mrItems->methodDetails
                            //     ->where('method_group', $curentMethodGroup)
                            //     ->where('method_row', $currentMethodRow);
                            //$data 插入玩法data
                            $data[$seriesId][$currentLotteryId]['child']
                            [$curentMethodGroup]['child'][$mrItems->method_row]['child'] = $methodData;
                        }
                    }
                }
            }
            self::saveTagsCacheData($redisKey, $data);
        }
        return $contll->msgOut(true, $data);
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
