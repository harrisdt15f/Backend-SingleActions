<?php

namespace App\Http\SingleActions\Backend\Users\Fund;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\Fund\FrontendUsersAccountsTypesParam;
use Illuminate\Http\JsonResponse;
use DB;

class AccountChangeTypeFieldDelAction
{
    protected $model;

    /**
     * @param  FrontendUsersAccountsType $frontendUsersAccountsType
     */
    public function __construct(FrontendUsersAccountsTypesParam $frontendUsersAccountsTypesParam)
    {
        $this->model = $frontendUsersAccountsTypesParam;
    }

    /**
     * 帐变类型字段添加删除
     * @param  BackEndApiMainController $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $typeField = $this->model->find($inputDatas['id']);
        if (is_null($typeField)) {
            return $contll->msgOut(false, [], '102300');
        }
        $typeField->delete();
        if ($typeField->errors()->messages()) {
            return $contll->msgOut(false, [], '400', $typeField->errors()->messages());
        }
        return $contll->msgout(true);
    }
}
