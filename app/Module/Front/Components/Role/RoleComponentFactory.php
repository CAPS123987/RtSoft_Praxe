<?php

namespace App\Module\Front\Components\Role;

use App\Model\Permission\Facades\PermissionFacade;
use App\Model\Role\Facades\RoleFacade;
use App\Model\Role\Facades\RolePermissionFacade;

class RoleComponentFactory
{
    public function __construct(
        private readonly RolePermissionFacade $rolePermissionFacade,
        private readonly PermissionFacade $permissionFacade,
        private readonly RoleFacade $roleFacade
    )
    {
    }
    public function create(?int $roleId = null): RoleComponent
    {
        return new RoleComponent(
            $this->rolePermissionFacade,
            $this->permissionFacade,
            $this->roleFacade,
            $roleId
        );
    }
}