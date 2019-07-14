<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 18:29:34
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 18:31:38
 */
namespace App\Http\SingleActions\Backend\Users\District;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\UsersRegion;
use Illuminate\Http\JsonResponse;

class RegionGetTownAction
{
    protected $model;

    /**
     * @param  UsersRegion  $usersRegion
     */
    public function __construct(UsersRegion $usersRegion)
    {
        $this->model = $usersRegion;
    }

    /**
     * 获取 镇(街道) 列表
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $isExist = $this->model::where(['region_level' => $inputDatas['region_level'], 'region_id' => $inputDatas['region_parent_id']])->exists();
        if ($isExist === false) {
            return $contll->msgOut(false, [], '101000');
        }
        $datas = $this->model::where(['region_level' => 4, 'region_parent_id' => $inputDatas['region_parent_id']])->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
