<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 20:26:17
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 17:05:52
 */
namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\User\FrontendUser;
use App\Models\User\Fund\FrontendUsersAccount;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserHandleCreateUserAction
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
     *创建总代与用户后台管理员操作
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $inputDatas['password'] = bcrypt($inputDatas['password']);
        $inputDatas['fund_password'] = bcrypt($inputDatas['fund_password']);
        $inputDatas['platform_id'] = $contll->currentPlatformEloq->platform_id;
        $inputDatas['sign'] = $contll->currentPlatformEloq->platform_sign;
        $inputDatas['vip_level'] = 0;
        $inputDatas['parent_id'] = 0;
        $inputDatas['register_ip'] = request()->ip();
        DB::beginTransaction();
        try {
            $user = $this->model::create($inputDatas);
            $user->rid = $user->id;
            $userAccountEloq = new FrontendUsersAccount();
            $userAccountData = [
                'user_id' => $user->id,
                'balance' => 0,
                'frozen' => 0,
                'status' => 1,
            ];
            $userAccountEloq = $userAccountEloq->fill($userAccountData);
            $userAccountEloq->save();
            $user->account_id = $userAccountEloq->id;
            $user->save();
            DB::commit();
            $data['name'] = $user->username;
            return $contll->msgOut(true, $data);
        } catch (Exception $e) {
            DB::rollBack();
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
//        $success['token'] = $user->createToken('前端')->accessToken;
    }
}
