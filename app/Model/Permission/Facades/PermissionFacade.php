<?php

namespace App\Model\Permission\Facades;

use App;
use App\Model\Permission\DTO\PermissionDTO;
use App\Model\Generics\Facades\DTOFacade;

/**
 * @extends DTOFacade<PermissionDTO>
 */
final class PermissionFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Permission\Repo\PermissionRepository $permissionRepository,
        private readonly App\Model\Permission\Mapper\PermissionMapper   $permissionMapper,
    ) {
        parent::__construct($permissionRepository, $permissionMapper);
    }
}

