<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 15:50:29
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:13:25
 */
namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Activity\BackendAdminMessageArticle;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ArticlesDeleteAction
{
    protected $model;

    /**
     * @param  BackendAdminMessageArticle  $backendAdminMessageArticle
     */
    public function __construct(BackendAdminMessageArticle $backendAdminMessageArticle)
    {
        $this->model = $backendAdminMessageArticle;
    }

    /**
     * 删除文章
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::find($inputDatas['id']);
        $sort = $pastDataEloq->sort;
        $picPathArr = explode('|', $pastDataEloq->pic_path);
        DB::beginTransaction();
        try {
            $pastDataEloq->delete();
            //排序
            $this->model::where('sort', '>', $sort)->decrement('sort');
            //删除图片
            $contll->deleteImg($picPathArr);
            DB::commit();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            DB::rollback();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
