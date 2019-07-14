<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-20 15:21:31
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-21 21:13:05
 */
namespace App\Http\SingleActions\Backend\Admin\Article;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\Activity\BackendAdminMessageArticle;
use Illuminate\Http\JsonResponse;

class ArticlesDetailAction
{
    protected $model;

    /**
     * @param  BackendAdminMessageArticle  $backendAdminMessageArticle
     */
    public function __construct(BackendAdminMessageArticle $backendAdminMessageArticle)
    {
        $this->model = $backendAdminMessageArticle;
    }

    /**
     * 文章列表
     * @param  BackEndApiMainController  $contll
     * @return JsonResponse
     */
    public function execute(BackEndApiMainController $contll): JsonResponse
    {
        $field = 'sort';
        $type = 'asc';
        $searchAbleFields = ['title', 'type', 'search_text', 'is_for_agent'];
        $datas = $contll->generateSearchQuery($this->model, $searchAbleFields, 0, null, null, $field, $type);
        return $contll->msgOut(true, $datas);
    }
}
