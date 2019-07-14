<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 17:41:48
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:19:34
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryRedirectBetList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PopularLotteriesDeleteAction
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
     * 删除热门彩票
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $pastData = $pastDataEloq;
        DB::beginTransaction();
        try {
            $pastDataEloq->delete();
            //重新排序
            $datas = $this->model::where('sort', '>', $pastData->sort)->decrement('sort');
            DB::commit();
            //删除图片
            $imageObj = new ImageArrange();
            $imageObj->deletePic(substr($pastData->pic_path, 1));
            //清除首页热门彩票缓存
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
