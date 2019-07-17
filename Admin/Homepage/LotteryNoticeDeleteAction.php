<?php

namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendLotteryNoticeList;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LotteryNoticeDeleteAction
{
    /**
     * 删除开奖公告的彩种
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $lotteriesEloq = FrontendLotteryNoticeList::find($inputDatas['id']);
        $sort = $lotteriesEloq->sort; //保存sort 删除数据后排在数据后面的sort自减1
        $iconPsth = $lotteriesEloq->icon_path; //保存icon路径 删除数据后删除图片
        DB::beginTransaction();
        $lotteriesEloq->delete();
        FrontendLotteryNoticeList::where('sort', '>', $sort)->decrement('sort'); //sort排后面的自减1
        if ($lotteriesEloq->errors()->messages()) {
            DB::rollback();
            return $contll->msgOut(false, [], '400', $lotteryNoticeELoq->errors()->messages());
        }
        DB::commit();
        $imageObj = new ImageArrange();
        $imageObj->deletePic(substr($iconPsth, 1)); //删除图片
        return $contll->msgOut(true);
    }
}
