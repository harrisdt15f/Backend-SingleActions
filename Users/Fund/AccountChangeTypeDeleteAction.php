<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 20:20:04
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 20:24:25
 */
namespace App\Http\SingleActions\Backend\Users\Fund;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsType;
use Exception;
use Illuminate\Http\JsonResponse;

class AccountChangeTypeDeleteAction
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
     * 删除帐变类型
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        try {
            $this->model::find($inputDatas['id'])->delete();
            return $contll->msgout(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
