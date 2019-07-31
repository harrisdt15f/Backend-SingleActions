<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use Exception;
use Illuminate\Http\JsonResponse;

class PopularLotteriesDetailAction
{
    protected $model;

    /**
     * @param  FrontendLotteryRedirectBetList  $frontendLotteryRedirectBetList
     */
    public function __construct(FrontendLotteryRedirectBetList $frontendLotteryRedirectBetList)
    {
        $this->model = $frontendLotteryRedirectBetList;
    }

    /**
     * 热门彩票列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $lotterieEloqs = $this->model::select('id', 'lotteries_id', 'pic_path', 'sort')->with('lotteries:id,cn_name')->orderBy('sort', 'asc')->get();
        $datas = [];
        foreach ($lotterieEloqs as $lotterie) {
            $data = [
                'id' => $lotterie->id,
                'lotteries_id' => $lotterie->lotteries_id,
                'cn_name' => $lotterie->lotteries->cn_name ?? null,
                'pic_path' => $lotterie->pic_path,
                'sort' => $lotterie->sort,
            ];
            $datas[] = $data;
        }
        return $contll->msgOut(true, $datas);
    }
}
