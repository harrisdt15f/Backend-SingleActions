<?php

namespace App\Http\SingleActions\Backend\Admin\Log;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Lib\Common\IpAddress;
use App\Models\Admin\SystemAddressIp;
use Illuminate\Http\JsonResponse;

class HandleLogGetAddressAction
{
    /**
     * IP获取地址
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $addressIpELoq = SystemAddressIp::where('ip', $inputDatas['ip'])->first();
        if ($addressIpELoq === null) {
            $ipAddressCla = new IpAddress();
            $addressIpELoq = $ipAddressCla->getAddress($inputDatas['ip']);
        }
        $data = [
            'ip' => $addressIpELoq->ip,
            'country' => $addressIpELoq->country,
            'region' => $addressIpELoq->region,
            'city' => $addressIpELoq->city,
            'county' => $addressIpELoq->county,
        ];
        return $contll->msgOut(true, $data);
    }
}
