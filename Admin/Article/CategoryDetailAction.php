<?php

namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
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
