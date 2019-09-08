<?php

namespace App\Http\SingleActions\Backend\Users;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\User\FrontendUser;
use App\Models\User\Fund\FrontendUsersAccount;
use App\Models\User\UserPublicAvatar;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Lib\Common\VerifyPassword;
use App\Lib\Common\VerifyFundPassword;
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
     * @param  array $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, array $inputDatas): JsonResponse
    {
        //检验密码与资金密码不能一致
        if ($inputDatas['password'] === $inputDatas['fund_password']) {
            return $contll->msgOut(false, [], '100122');
        }
        //检验密码
        if (VerifyPassword::verifyPassword($contll, $inputDatas['password'], 1)) {
            return VerifyPassword::verifyPassword($contll, $inputDatas['password'], 1);
        }
        //检验资金密码
        if (VerifyFundPassword::verifyFundPassword($contll, $inputDatas['fund_password'],1)) {
            return VerifyFundPassword::verifyFundPassword($contll, $inputDatas['fund_password'], 1);
        }
        $inputDatas['password'] = bcrypt($inputDatas['password']);
        $inputDatas['fund_password'] = bcrypt($inputDatas['fund_password']);
        $inputDatas['platform_id'] = $contll->currentPlatformEloq->platform_id;
        $inputDatas['sign'] = $contll->currentPlatformEloq->platform_sign;
        $inputDatas['vip_level'] = 0;
        $inputDatas['parent_id'] = 0;
        $inputDatas['register_ip'] = request()->ip();
        $inputDatas['pic_path'] = UserPublicAvatar::getRandomAvatar();
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
            return $contll->msgOut(false, [], $e->getCode(), $e->getMessage());
        }
//        $success['token'] = $user->createToken('前端')->accessToken;
    }
}
