<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 19:58:08
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:22:02
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\SystemConfiguration;
use Illuminate\Http\JsonResponse;

class ConfiguresGetConfiguresListAction
{
    protected $model;

    /**
     * @param  SystemConfiguration  $systemConfiguration
     */
    public function __construct(SystemConfiguration $systemConfiguration)
    {
        $this->model = $systemConfiguration;
    }

    /**
     * 获取全部配置
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $partnerSysConfigEloq = $this->model::select('id', 'parent_id', 'pid', 'sign', 'name', 'description', 'value', 'add_admin_id', 'last_update_admin_id', 'status', 'created_at', 'updated_at')->where('display', 1)->get();
        $data = [];
        foreach ($partnerSysConfigEloq as $partnerSysConfigItem) {
            if ($partnerSysConfigItem->parent_id === 0) {
                $data[$partnerSysConfigItem->id] = $partnerSysConfigItem->toArray();
                $data[$partnerSysConfigItem->id]['sub'] = $partnerSysConfigItem->childs->toArray();
            }
        }
        return $contll->msgOut(true, $data);
    }
}
