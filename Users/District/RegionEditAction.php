<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 19:39:16
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-27 10:50:53
 */
namespace App\Http\SingleActions\Backend\Users\District;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\UsersRegion;
use Illuminate\Http\JsonResponse;

class RegionEditAction
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
     * 编辑行政区
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $isExist = $this->model::where([
            ['region_id', '=', $inputDatas['region_id']],
            ['id', '!=', $inputDatas['id']],
        ])->exists();
        if ($isExist === true) {
            return $contll->msgOut(false, [], '101001');
        }
        try {
            $editDataEloq = $this->model::find($inputDatas['id']);
            $editDataEloq->region_id = $inputDatas['region_id'];
            $editDataEloq->region_name = $inputDatas['region_name'];
            $editDataEloq->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
