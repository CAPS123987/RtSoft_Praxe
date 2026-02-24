<?php

namespace App\Module\Front\Components\EditUser;

use App\Model\Permission\PermissionList;
use App\Model\Role\Facades\RoleFacade;
use App\Model\User\Facades\UserFacade;
use Nette\Security\Passwords;

class EditUserComponentFactory
{
    public function __construct(
        private readonly Passwords $passwords,
        private readonly PermissionList $perms,
        private readonly RoleFacade $roleFacade,
        private readonly UserFacade $userFacade,
    )
    {
    }

    public function create(int $userId): EditUserComponent
    {
        return new EditUserComponent(
            $this->passwords,
            $this->perms,
            $this->roleFacade,
            $this->userFacade,
            $userId,
        );
    }
}

