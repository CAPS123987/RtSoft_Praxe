<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model\Permission\PermissionList;
use App\Model\Role\Facades\RoleFacade;
use App\Model\Role\Facades\RolePermissionFacade;
use App\Model\User\Facades\UserFacade;
use App\Module\Front\Components\AddUser\AddUserComponent;
use App\Module\Front\Components\AddUser\AddUserComponentFactory;
use App\Module\Front\Components\EditUser\EditUserComponent;
use App\Module\Front\Components\EditUser\EditUserComponentFactory;
use App\Module\Front\Components\Role\RoleComponent;
use App\Module\Front\Components\Role\RoleComponentFactory;

class AdminPresenter extends BasePresenter
{
    public function __construct(
        private PermissionList $perms,
        private RoleFacade $roleFacade,
        private UserFacade $userFacade,
        private RolePermissionFacade $rolePermissionFacade,
        private RoleComponentFactory $roleComponentFactory,
        private AddUserComponentFactory $addUserComponentFactory,
        private EditUserComponentFactory $editUserComponentFactory,
    ) {
        parent::__construct($perms);
    }

    public function startup(): void
    {
        parent::startup();

        if (!parent::isAllowed($this->perms->adminPanel)) {
            $this->redirect('Sign:in');
        }
    }

    protected function createComponentEditUserForm(): EditUserComponent
    {
        $id = (int) $this->getParameter('id');
        return $this->editUserComponentFactory->create($id);
    }

    public function renderUserList()
    {
        $this->template->users = $this->userFacade->getAllDTO();
    }

    public function renderEditUser(int $id)
    {
        $userDTO = $this->userFacade->getDTOById($id);
        $this->template->userDTO = $userDTO;
    }

    public function renderRoleList()
    {
        $rolePairs = [];
        foreach ($this->roleFacade->getAllDTO() as $role) {
            $rolePairs[] = [$role,$this->rolePermissionFacade->getRolePermissionsByRoleId($role->id)];
        }
        $this->template->roles = $rolePairs;
    }


    protected function createComponentRoleEditForm(): RoleComponent
    {
        $id = (int) $this->getParameter('id');
        return $this->roleComponentFactory->create(roleId: $id);
    }

    protected function createComponentAddRoleForm(): RoleComponent
    {
        return $this->roleComponentFactory->create();
    }

    protected function createComponentAddUserForm(): AddUserComponent
    {
        return $this->addUserComponentFactory->create();
    }

    public function renderEditRole(int $id)
    {
        $roleDTO = $this->roleFacade->getDTOById($id);
        $this->template->roleDTO = $roleDTO;
    }

    public function renderAddRole()
    {

    }
}