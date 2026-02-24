<?php

namespace App\Module\Front\Components\Role;

use App\Model\Permission\Facades\PermissionFacade;
use App\Model\Role\DTO\RoleDTO;
use App\Model\Role\Facades\RoleFacade;
use App\Model\Role\Facades\RolePermissionFacade;
use http\Exception\RuntimeException;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class RoleComponent extends Control
{
    public function __construct(
        private readonly RolePermissionFacade $rolePermissionFacade,
        private readonly PermissionFacade $permissionFacade,
        private readonly RoleFacade $roleFacade,
        private readonly ?int $roleId = null,
    )
    {
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/RoleComponent.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $roleId = $this->roleId;
        $form = $this->getForm($roleId);
        if(empty($roleId)) {

            $form->addText("name", "name")->setRequired();
            $form->addSubmit("submit", "Přidat");

            $form->onSuccess[] = [$this, 'addRoleFormSucceeded'];
        } else {
            $form->addSubmit("submit", "Upravit");

            $form->onSuccess[] = function (Form $form, array $data) use ($roleId) {
                $this->editRoleFormSucceeded($form, $data, $roleId);
            };
        }
        return $form;
    }

    public function editRoleFormSucceeded(Form $form, array $data, int $roleId): void
    {
        unset($data["submit"]);
        $this->editRole($roleId, $data);
    }


    public function addRoleFormSucceeded(Form $form, array $data): void
    {
        $roleDTO = RoleDTO::create(
            id: null,
            name: $data["name"],
        );
        $result = $this->roleFacade->insertDTO($roleDTO);
        $roleId = intval($result->id);

        $insertedRoleDTO = RoleDTO::create(
            id: $roleId,
            name: $data["name"],
        );

        unset($data["name"], $data["submit"]);

        $this->editRoleWithDTO($insertedRoleDTO, $roleId, $data);
    }

    public function editRole(int $roleId, array $data): void {

        $roleDTO = $this->roleFacade->getDTOById($roleId);
        $this->editRoleWithDTO($roleDTO, $roleId, $data);
    }

    private function editRoleWithDTO(RoleDTO $roleDTO, int $roleId, array $data): void {
        try {
            $this->rolePermissionFacade->updateRolePermissions($roleDTO, $data);
            $this->getPresenter()->flashMessage("Oprávnění pro roli úspěšně upraveno", 'success');
        }
        catch(\Exception $e) {
            $this->getPresenter()->flashMessage("Chyba ". $e->getMessage(), 'error');
            throw new \Latte\RuntimeException($e->getMessage());
        }
        $this->getPresenter()->redirect('Admin:editRole', ['id' => strval($roleId)]);
    }

    private function getForm(?int $roleId): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form;

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