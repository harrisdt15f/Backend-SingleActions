<?php

namespace App\Http\SingleActions\Backend\Users\District;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\UsersRegion;
use Illuminate\Http\JsonResponse;

class RegionAddAction
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
     * 添加行政区
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $isExist = $this->model::where([
            'region_parent_id' => $inputDatas['region_parent_id'],
            'region_name' => $inputDatas['region_name'],
        ])->orwhere('region_id', $inputDatas['region_id'])->exists();
        if ($isExist === true) {
            return $contll->msgOut(false, [], '101001');
        }
        try {
            $configure = new $this->model();
            $configure->fill($inputDatas);
            $configure->save();
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
