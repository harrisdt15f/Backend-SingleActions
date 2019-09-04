<?php

namespace App\Http\SingleActions\Backend;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class BackendAuthSearchUserAction
{
    protected $model;

    /**
     * @param  BackendAdminUser $backendAdminUser
     */
    public function __construct(BackendAdminUser $backendAdminUser)
    {
        $this->model = $backendAdminUser;
    }

    /**
     * @param  BackEndApiMainController $contll
     * @param  array                    $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, array $inputDatas): JsonResponse
    {
        $targetUserEloq = $this->model::where([
            ['email', '=', $inputDatas['email']],
        ])->first();
        if ($targetUserEloq === null) {
            return $contll->msgOut(false, [], '100004');
        }
        return $contll->msgOut(true, $targetUserEloq);
    }
}
