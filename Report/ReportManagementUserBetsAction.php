<?php

namespace App\Http\SingleActions\Backend\Report;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Models\User\FrontendUser;

class ReportManagementUserBetsAction
{
    /**
     * 玩家注单报表
     * @param   BackEndApiMainController  $contll
     * @param   array $inputDatas
     * @return  JsonResponse
     */
    public function execute(BackEndApiMainController $contll, array $inputDatas): JsonResponse
    {
        if ((int) $inputDatas['get_sub'] === 1) {// 搜索下级
            $userIds = $this->getUserIds($inputDatas['username']);

            $contll->inputs['extra_where']['method'] = 'whereIn';
            $contll->inputs['extra_where']['key'] = 'user_id';
            $contll->inputs['extra_where']['value'] = $userIds;
            unset($contll->inputs['username']);
        }
        $projectEloq = new Project();
        $searchAbleFields = [
            'serial_number',
            'username',
            'series_id',
            'lottery_sign',
            'method_sign',
            'is_tester',
            'issue',
            'mode',
            'status'
        ];
        $field = 'id';
        $type = 'desc';
        $datas = $contll->generateSearchQuery($projectEloq, $searchAbleFields, 0, null, [], $field, $type);
        return $contll->msgOut(true, $datas);
    }

    public function getUserIds($username)
    {
        $userELoq = FrontendUser::nameGetUser($username);
        if ($userELoq !== null) {
            $userIds = FrontendUser::getSubIds($userELoq->id);
        } else {
            $userIds = [];
        }
        return $userIds;
    }
}
