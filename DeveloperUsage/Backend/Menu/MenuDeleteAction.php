<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 11:46:12
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 11:59:11
 */
namespace App\Http\SingleActions\Backend\DeveloperUsage\Backend\Menu;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\DeveloperUsage\Menu\BackendSystemMenu;
use Exception;
use Illuminate\Http\JsonResponse;

class MenuDeleteAction
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
        $menuEloq = new $this->model;
        $toDelete = $inputDatas['toDelete'];
        if (!empty($toDelete)) {
            try {
                $datas = $menuEloq->find($toDelete)->each(function ($product, $key) {
                    $data[] = $product->toArray();
                    $product->delete();
                    return $data;
                });
                $menuEloq->refreshStar();
                return $contll->msgOut(true, $datas);
            } catch (Exception $e) {
                return $contll->msgOut(false, [], '0002', $e->getMessage());
            }
        }
    }
}
