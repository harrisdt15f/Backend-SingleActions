<?php

namespace App\Http\SingleActions\Backend\Admin;

use App\Http\Controllers\BackendApi\BackEndApiMainController;
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
