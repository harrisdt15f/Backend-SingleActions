<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 11:49:09
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 11:59:41
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Menu;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use Exception;
use Illuminate\Http\JsonResponse;

class MenuEditAction
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
        $menuEloq = $this->model::find($inputDatas['menuId']);
        $menuEloq->label = $inputDatas['label'];
        $menuEloq->en_name = $inputDatas['en_name'];
        $menuEloq->display = $inputDatas['display'];
        $menuEloq->icon = $inputDatas['icon'] ?? null;
        if ($parent === true) {
            $menuEloq->route = '#';
            $menuEloq->pid = 0;
        } else {
            $menuEloq->route = $inputDatas['route'];
            $menuEloq->pid = $inputDatas['parentId'];
        }
        $data = $menuEloq->toArray();
        if ($menuEloq->save()) {
            $menuEloq->refreshStar();
            return $contll->msgOut(true, $data);
        } else {
            return $contll->msgOut(false, [], '100801');
        }
    }
}
