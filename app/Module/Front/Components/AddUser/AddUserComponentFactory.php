<?php

namespace App\Module\Front\Components\AddUser;

use App\Model\Permission\PermissionList;
use App\Model\Role\Facades\RoleFacade;
use App\Model\User\Facades\UserFacade;
use Nette\Security\Passwords;

class AddUserComponentFactory
{
    public function __construct(
        private readonly Passwords $passwords,
        private readonly PermissionList $perms,
        private readonly RoleFacade $roleFacade,
        private readonly UserFacade $userFacade,
    )
    {
    }

    public function create(): AddUserComponent
    {
        return new AddUserComponent(
            $this->passwords,
            $this->perms,
            $this->roleFacade,
            $this->userFacade,
        );
    }
}

