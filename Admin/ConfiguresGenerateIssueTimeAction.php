<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-21 20:27:38
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-26 16:55:22
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\SystemConfiguration;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfiguresGenerateIssueTimeAction
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
     * 配置获取奖期时间
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $pastDataEloq = $this->model::where('sign', 'generate_issue_time')->first();
        try {
            if ($pastDataEloq !== null) {
                $pastDataEloq->value = $inputDatas['value'];
                $pastDataEloq->save();
                if (Cache::has('generateIssueTime')) {
                    Cache::forget('generateIssueTime');
                }
            } else {
                $bool = $this->createIssueConfigure($inputDatas['value']);
                if ($bool === false) {
                    return $contll->msgOut(false, [], '100702');
                }
            }
            return $contll->msgOut(true);
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误码，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }

    /**
     * 创建生成奖期时间的配置
     * @param  date $time
     * @return bool
     */
    public function createIssueConfigure($time): bool
    {
        DB::beginTransaction();
        try {
            //生成父级 奖期相关 系统配置
            $addData = [
                'parent_id' => 0,
                'pid' => 1,
                'sign' => 'issue',
                'name' => '奖期相关',
                'description' => '奖期相关的所有配置',
                'add_admin_id' => $contll->partnerAdmin->id,
                'last_update_admin_id' => $contll->partnerAdmin->id,
                'status' => 1,
                'display' => 0,
            ];
            $configureEloq = new $this->model();
            $configureEloq->fill($addData);
            $configureEloq->save();
            //生成子级 生成奖期时间 系统配置
            $data = [
                'parent_id' => $configureEloq->id,
                'pid' => 1,
                'sign' => 'generate_issue_time',
                'name' => '生成奖期时间',
                'description' => '每天自动生成奖期的时间',
                'value' => $time,
                'add_admin_id' => $contll->partnerAdmin->id,
                'last_update_admin_id' => $contll->partnerAdmin->id,
                'status' => 1,
                'display' => 0,
            ];
            $issueTimeEloq = new $this->model();
            $issueTimeEloq->fill($data);
            $issueTimeEloq->save();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
