<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 15:55:37
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 15:59:31
 */
namespace App\Http\SingleActions\Backend\Game\Lottery;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Game\Lottery\LotterySerie;
use Illuminate\Http\JsonResponse;

class LotteriesSeriesListsAction
{
    protected $model;

    /**
     * @param  LotterySerie  $lotterySerie
     */
    public function __construct(LotterySerie $lotterySerie)
    {
        $this->model = $lotterySerie;
    }

    /**
     * 获取系列接口
     * @param   BackEndApiMainController  $contll
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $seriesData = $this->model::select('series_name', 'title', 'status', 'encode_splitter')->get();
        return $contll->msgOut(true, $seriesData);
    }
}
