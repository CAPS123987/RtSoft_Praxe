<?php

namespace App\Model\Factories;

use App\Model\Permission\Facades\PermissionFacade;
use App\Model\Permission\PermissionList;
use App\Model\Role\Facades\RoleFacade;
use App\Model\Role\Facades\RolePermissionFacade;
use App\Module\Front\Presenters\BasePresenter;
use Nette\Forms\Form;

class RoleEditFormFactory
{
    public function __construct(
        private readonly RoleFacade $roleFacade,
        private readonly PermissionList $perms,
        private readonly RolePermissionFacade $rolePermissionFacade,
        private readonly PermissionFacade $permissionFacade,
    )
    {
    }

    /**
     * needs to add $form->onSuccess[] = [$this, 'editRoleFormSucceeded']; <br>
     * and $form->addSubmit('send', 'Upravit');
     *
     * @param BasePresenter $presenter
     * @param int $roleId
     * @return Form
     */
    public function create(BasePresenter $presenter, int|null $roleId): Form
    {
        $form = new \Nette\Application\UI\Form;

        $allPermissions = $this->permissionFacade->getAllDTO();

        try {
            $rolePermissions = $this->rolePermissionFacade->getRolePermissionsByRoleId(intval($roleId));
        } catch (\Exception $e) {
            $rolePermissions = [];
        }

        foreach ($allPermissions as $permission) {
            $form->addCheckbox($permission->name, $permission->name)
                ->value = in_array($permission, $rolePermissions);
        }

        return $form;
    }
}