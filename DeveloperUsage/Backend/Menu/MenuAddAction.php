<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 11:39:29
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 11:58:16
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Menu;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use Exception;
use Illuminate\Http\JsonResponse;

class MenuAddAction
{
    protected $model;

    /**
     * @param  BackendSystemMenu  $backendSystemMenu
     */
    public function __construct(BackendSystemMenu $backendSystemMenu)
    {
        $this->model = $backendSystemMenu;
    }

    /**
     * @param  BackEndApiMainController  $contll
     * @param  $inputDatas
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll, $inputDatas): JsonResponse
    {
        $parent = false;
        if (isset($inputDatas['isParent']) && $inputDatas['isParent'] === '1') {
            $parent = true;
        }
        $MenuEloq = $this->model::where('label', $inputDatas['label'])->first();
        if ($MenuEloq !== null) {
            return $contll->msgOut(false, [], '100800');
        }
        $menuEloq = new BackendSystemMenu();
        $menuEloq->label = $inputDatas['label'];
        $menuEloq->en_name = $inputDatas['en_name'];
        $menuEloq->route = $inputDatas['route'];
        $menuEloq->display = $inputDatas['display'];
        $menuEloq->icon = $inputDatas['icon'] ?? null;
        $menuEloq->sort = $inputDatas['sort'];
        if ($parent === false) {
            $menuEloq->pid = $inputDatas['parentId'];
            $menuEloq->level = $inputDatas['level'];
        }
        try {
            $menuEloq->save();
            $menuEloq->refreshStar();
            return $contll->msgOut(true, $menuEloq->toArray());
        } catch (Exception $e) {
            $errorObj = $e->getPrevious()->getPrevious();
            [$sqlState, $errorCode, $msg] = $errorObj->errorInfo; //［sql编码,错误妈，错误信息］
            return $contll->msgOut(false, [], $sqlState, $msg);
        }
    }
}
