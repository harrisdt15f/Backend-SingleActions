<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 15:44:35
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 16:54:37
 */
namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Activity\BackendAdminMessageArticle;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ArticlesEditAction
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
     * 编辑文章
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $issetTitle = $this->model::where('title', $inputDatas['title'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($issetTitle === true) {
            return $contll->msgOut(false, [], '100500');
        }
        try {
            $pastEloq = $this->model::find($inputDatas['id']);
            //插入 backend_admin_audit_flow_lists 审核表
            $auditFlowId = $contll->insertAuditFlow($inputDatas['apply_note']);
            $pastEloq->audit_flow_id = $auditFlowId;
            //
            $pastPicPath = $pastEloq->pic_path;
            $editDatas = $inputDatas;
            unset($editDatas['pic_name'], $editDatas['apply_note']);
            $contll->editAssignment($pastEloq, $editDatas);
            $pastEloq->status = 0;
            $pastEloq->last_update_admin_id = $contll->partnerAdmin->id;
            //查看是否修改图片
            $new_pic_path = $inputDatas['pic_path'];
            if ($new_pic_path != $pastPicPath) {
                //销毁缓存
                $contll->deleteCachePic($inputDatas['pic_name']);
                //删除原图
                $pastPicPathArr = explode('|', $pastPicPath);
                $contll->deleteImg($pastPicPathArr);
                //
                $pastEloq->pic_path = implode('|', $new_pic_path);
            }
            $pastEloq->save();
            //发送站内消息给管理员审核
            $contll->sendMessage();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
