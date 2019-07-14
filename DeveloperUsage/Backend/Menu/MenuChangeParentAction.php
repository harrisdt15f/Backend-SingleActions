<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 11:52:40
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 13:45:58
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Menu;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use Illuminate\Http\JsonResponse;

class MenuChangeParentAction
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
        $parseDatas = json_decode($inputDatas['dragResult'], true);
        $itemProcess = [];
        $atLeastOne = false;
        if (!empty($parseDatas)) {
            $menuELoq = new $this->model;
            $itemProcess = $menuELoq->changeParent($parseDatas);
            return $contll->msgOut(true, $itemProcess);
        }
    }
}
