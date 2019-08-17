<?php

namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\User\FrontendUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserHandleUsersInfoAction
{
    protected $model;

    /**
     * @param  FrontendUser $frontendUser
     */
    public function __construct(FrontendUser $frontendUser)
    {
        $this->model = $frontendUser;
    }

    /**
     * 用户管理的所有用户信息表
     * @param  BackEndApiMainController $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        //target model to join
        $fixedJoin = 1; //number of joining tables
        $withTable = 'account';
        $searchAbleFields = [
            'username',
            'type',
            'vip_level',
            'is_tester',
            'frozen_type',
            'prize_group',
            'level_deep',
            'register_ip',
            'parent_id',
        ];
        $withSearchAbleFields = ['balance'];
        $data = $contll->generateSearchQuery(
            $this->model,
            $searchAbleFields,
            $fixedJoin,
            $withTable,
            $withSearchAbleFields
        ) ;
        $isSetTotal = isset($contll->inputs['total_members']) ? true : false;
        $isSetParentName = isset($contll->inputs['parent_name']) ? true : false;

        if ($isSetTotal || $isSetParentName) {
            //要名字
            if ($isSetParentName) {
                if (count($data) > 0) {
                    //待功能验收后进一步优化
                    $totalData = $this->model->get()->toArray();
                    $totalData = array_combine(array_column($totalData, 'id'), $totalData);
                }
            }
            //要总数
            if ($isSetTotal) {
                $dataChange = $data->toArray()['data'];
                $dataChange = array_combine(array_column($dataChange, 'id'), $dataChange);
                $dataChange = array_keys($dataChange);
                $specData = DB::table('frontend_users_specific_infos')
                    ->whereIn('user_id', $dataChange)
                    ->get()->toArray();
                $specData = array_combine(array_column($specData, 'user_id'), $specData);
            }
            foreach ($data as $k => $v) {
                if ($isSetParentName) {
                    $parentName = self::getParentUserName($totalData, $v);
                    if ($parentName['status'] === false) {
                        return $contll->msgOut(false, $parentName['data'], '100110');
                    }
                    $data[$k]['parent_username'] = $parentName['data'];
                }
                if ($isSetTotal) {
                    $data[$k]['total_members'] = $specData[$v->id]->total_members;
                }
            }
        }
        return $contll->msgOut(true, $data);
    }

    /**
     * 将用户的的父ID变为用户名
     * @param  $totalData
     * @param  $parentKeys
     * @return Array
     */
    public function getParentUserName($totalData, $parentKeys): Array
    {
        $res = array(
            'status' => true,
            'data' => ''
        );
        $arr = explode("|", trim($parentKeys['rid'], '|'));
        $parentUserName = '';
        if (count($arr) < 1) {
            return $res;
        }
        foreach ($arr as $v1) {
            if (!isset($totalData[$v1])) {
                $res['status'] = false;
                $res['data'] = $parentKeys;
                $res['error'] = '该用户的上级ID不存在,请联系管理员';
                return $res;
            }
            $parentUserName .= ',' . $totalData[$v1]['username'];
        }
        $res['data'] = trim($parentUserName, ',');
        return $res;
    }
}
