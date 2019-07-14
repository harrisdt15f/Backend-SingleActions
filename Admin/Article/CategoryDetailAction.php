<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 18:06:26
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:15:09
 */
namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Activity\FrontendInfoCategorie;
use Illuminate\Http\JsonResponse;

class CategoryDetailAction
{
    protected $model;

    /**
     * @param  FrontendInfoCategorie  $frontendInfoCategorie
     */
    public function __construct(FrontendInfoCategorie $frontendInfoCategorie)
    {
        $this->model = $frontendInfoCategorie;
    }

    /**
     * 分类管理列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $datas = $this->model::from('frontend_info_categories as self')->leftJoin('frontend_info_categories as secondary', 'self.parent', '=', 'secondary.id')->select('self.*', 'secondary.title as parent_title')->get()->toArray();
        return $contll->msgOut(true, $datas);
    }
}
