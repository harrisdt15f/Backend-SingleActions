<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-26 18:16:48
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 18:19:07
 */
namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminAccessGroup;
use App\Models\Admin\BackendAdminUser;
use App\Models\Admin\Fund\BackendAdminRechargePocessAmount;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BackendAuthRegisterAction
{
    /**
     * Register api
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $group = BackendAdminAccessGroup::find($inputDatas['group_id']);
        $role = $group->role == '*' ? Arr::wrap($group->role) : Arr::wrap(json_decode($group->role, true));
        $isManualRecharge = false;
        $fundOperation = BackendSystemMenu::select('id')->where('route', '/manage/recharge')->first()->toArray();
        $isManualRecharge = in_array($fundOperation['id'], $role, true);
        $input = $inputDatas;
        $input['password'] = bcrypt($input['password']);
        $input['platform_id'] = $contll->currentPlatformEloq->platform_id;
        $user = BackendAdminUser::create($input);
        if ($isManualRecharge === true) {
            $insertData = ['admin_id' => $user->id];
            $fundOperationEloq = new BackendAdminRechargePocessAmount();
            $fundOperationEloq->fill($insertData);
            $fundOperationEloq->save();
        }
        $credentials = request(['email', 'password']);
        $token = $contll->currentAuth->attempt($credentials);
        $success['token'] = $token;
        $success['name'] = $user->name;
        return $contll->msgOut(true, $success);
    }
}
