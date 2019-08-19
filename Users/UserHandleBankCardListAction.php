<?php

namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\User\Fund\FrontendUsersBankCard;
use Illuminate\Http\JsonResponse;

class UserHandleBankCardListAction
{
    protected $model;

    /**
     * @param  FrontendUsersBankCard  $frontendUsersBankCard
     */
    public function __construct(FrontendUsersBankCard $frontendUsersBankCard)
    {
        $this->model = $frontendUsersBankCard;
    }
    /**
     * 用户银行卡列表
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $searchAbleFields = ['bank_name', 'branch', 'username', 'owner_name', 'card_number', 'bank_sign', 'status'];
        $data = $contll->generateSearchQuery($this->model, $searchAbleFields);
        return $contll->msgOut(true, $data);
    }
}
