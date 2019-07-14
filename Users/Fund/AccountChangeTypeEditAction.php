<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 20:15:47
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 20:24:30
 */
namespace App\Http\SingleActions\Backend\Users\Fund;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsType;
use Exception;
use Illuminate\Http\JsonResponse;

class AccountChangeTypeEditAction
{
    protected $model;

    /**
     * @param  FrontendUsersAccountsType  $frontendUsersAccountsType
     */
    public function __construct(FrontendUsersAccountsType $frontendUsersAccountsType)
    {
        $this->model = $frontendUsersAccountsType;
    }

    /**
     * 编辑帐变类型
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $isExistSign = $this->model::where('sign', $inputDatas['sign'])->where('id', '!=', $inputDatas['id'])->exists();
        if ($isExistSign === true) {
            return $contll->msgout(false, [], '101200');
        }
        $editData = $inputDatas;
        $param = implode(',', $inputDatas['param']);
        $editData['param'] = $param;
        $pastEloq = $this->model::find($inputDatas['id']);
        $contll->editAssignment($pastEloq, $editData);
        $pastEloq->save();
        if ($pastEloq->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $pastEloq->errors()->messages());
        }
        return $contll->msgout(true);
    }
}
