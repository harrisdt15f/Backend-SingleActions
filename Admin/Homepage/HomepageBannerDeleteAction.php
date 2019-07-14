<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 15:40:23
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:16:50
 */
namespace App\Http\SingleActions\Backend\Admin\Homepage;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\ImageArrange;
use App\Models\Admin\Homepage\FrontendPageBanner;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomepageBannerDeleteAction
{
    protected $model;

    /**
     * @param  FrontendPageBanner  $frontendPageBanner
     */
    public function __construct(FrontendPageBanner $frontendPageBanner)
    {
        $this->model = $frontendPageBanner;
    }

    /**
     * 删除首页轮播图
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
            $imageObj = new ImageArrange();
            $pastDataEloq->delete();
            //往后的sort重新排序
            $this->model::where('sort', '>', $pastData->sort)->decrement('sort');
            DB::commit();
            $deleteStatus = $imageObj->deletePic(substr($pastData->pic_path, 1));
            //清除首页banner缓存
            $contll->deleteCache();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
