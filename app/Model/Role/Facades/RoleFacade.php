<?php

namespace App\Model\Role\Facades;

use App;
use App\Model\Role\DTO\RoleDTO;
use App\Model\Generics\Facades\DTOFacade;

/**
 * @extends DTOFacade<RoleDTO>
 */
final class RoleFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Role\Repo\RoleRepository $roleRepository,
        private readonly App\Model\Role\Mapper\RoleMapper   $roleMapper,
    ) {
        parent::__construct($roleRepository, $roleMapper);
    }
}

