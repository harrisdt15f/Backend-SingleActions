<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Admin\Homepage\FrontendLotteryFnfBetableList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularMethodsDeleteAction
{
    protected $model;

    /**
     * @param  FrontendLotteryFnfBetableList  $frontendLotteryFnfBetableList
     */
    public function __construct(FrontendLotteryFnfBetableList $frontendLotteryFnfBetableList)
    {
        $this->model = $frontendLotteryFnfBetableList;
    }

    /**
     * 删除热门玩法
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        DB::beginTransaction();
        try {
            $pastDataEloq = $this->model::find($inputDatas['id']);
            $sort = $pastDataEloq->sort;
            $pastDataEloq->delete();
            //重新排序
            $datas = $this->model::where('sort', '>', $sort)->decrement('sort');
            DB::commit();
            //清除首页热门玩法缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
