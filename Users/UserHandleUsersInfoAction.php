<?php

namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\User\FrontendUser;
use Illuminate\Http\JsonResponse;

class UserHandleUsersInfoAction
{
    protected $model;

    /**
     * @param  FrontendUser  $frontendUser
     */
    public function __construct(FrontendUser $frontendUser)
    {
        $this->model = $frontendUser;
    }

    /**
     * 用户管理的所有用户信息表
     * @param  BackEndApiMainController  $contll
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
        ];
        $withSearchAbleFields = ['balance'];
        $data = $contll->generateSearchQuery($this->model, $searchAbleFields, $fixedJoin, $withTable,
            $withSearchAbleFields);
        return $contll->msgOut(true, $data);
    }
}
