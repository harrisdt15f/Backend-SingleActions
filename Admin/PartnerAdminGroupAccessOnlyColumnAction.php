<?php

/**
 * @Author: LingPh
 * @Date:   2019-06-24 10:50:30
 * @Last Modified by:   LingPh
 * @Last Modified time: 2019-06-24 11:18:20
 */
namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\backendApi\BackEndApiMainController;
use App\Models\Admin\BackendAdminAccessGroup;

class PartnerAdminGroupAccessOnlyColumnAction
{
    protected $model;

    /**
     * @param  BackendAdminAccessGroup  $backendAdminAccessGroup
     */
    public function __construct(BackendAdminAccessGroup $backendAdminAccessGroup)
    {
        $this->model = $backendAdminAccessGroup;
    }

    /**
     * @param  BackEndApiMainController  $contll
     * @return array
     */
    public function execute(BackEndApiMainController $contll): array
    {
        $partnerAdminAccess = new $this->model();
        $column = $partnerAdminAccess->getTableColumns();
        $column = array_values(array_diff($column, $contll->postUnaccess));
        return $column;
    }
}
