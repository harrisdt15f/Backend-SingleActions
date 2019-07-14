<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:23:08
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:19:11
 */
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
        $lotterieEloqs = $this->model::select('id', 'lotteries_id', 'pic_path', 'sort')->with(['lotteries' => function ($query) {
            $query->select('id', 'cn_name');
        }])->orderBy('sort', 'asc')->get();
        $datas = [];
        foreach ($lotterieEloqs as $lotterie) {
            $data = [
                'id' => $lotterie->id,
                'pic_path' => $lotterie->pic_path,
                'cn_name' => $lotterie->lotteries->cn_name,
                'sort' => $lotterie->sort,
            ];
            $datas[] = $data;
        }
        return $contll->msgOut(true, $datas);
    }
}
