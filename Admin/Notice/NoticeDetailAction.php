<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:29:34
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:21:19
 */
namespace App\Http\SingleActions\Backend\Admin\Notice;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Notice\FrontendMessageNotice;
use Illuminate\Http\JsonResponse;

class NoticeDetailAction
{
    protected $model;

    /**
     * @param  FrontendMessageNotice  $frontendMessageNotice
     */
    public function __construct(FrontendMessageNotice $frontendMessageNotice)
    {
        $this->model = $frontendMessageNotice;
    }

    /**
     * 公告列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $noticeDatas = $this->model::select('id', 'type', 'title', 'content', 'start_time', 'end_time', 'sort', 'status', 'admin_id')->with('admin')->orderBy('sort', 'asc')->get()->toArray();
        foreach ($noticeDatas as $key => $data) {
            $noticeDatas[$key]['admin_name'] = $data['admin']['name'];
            unset($noticeDatas[$key]['admin_id'], $noticeDatas[$key]['admin']);
        }
        return $contll->msgOut(true, $noticeDatas);
    }
}
