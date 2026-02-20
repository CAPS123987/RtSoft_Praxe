<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model\Factories\RoleEditFormFactory;
use App\Model\Permission\Facades\PermissionFacade;
use App\Model\Permission\PermissionList;
use App\Model\Role\DTO\RoleDTO;
use App\Model\Role\Facades\RoleFacade;
use App\Model\Role\Facades\RolePermissionFacade;
use App\Model\User\DTO\UserDTO;
use App\Model\User\Facades\UserFacade;
use App\Model\User\Facades\UserRoleFacade;
use Exception;
use http\Exception\RuntimeException;
use Nette;
use Nette\Application\UI\Form;

class AdminPresenter extends BasePresenter
{
    public function __construct(
        private PermissionList $perms,
        private RoleFacade $roleFacade,
        private Nette\Security\Passwords $passwords,
        private UserFacade $userFacade,
        private UserRoleFacade $userRoleFacade,
        private RolePermissionFacade $rolePermissionFacade,
        private PermissionFacade $permissionFacade,
        private RoleEditFormFactory $roleEditFormFactory,
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

    public function createComponentAddUserForm() {

        $form = new Form;
        $form->addText('name', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte heslo.');

        $this->roleFacade->getAllDTO();
        $options = [];
        foreach ($this->roleFacade->getAllDTO() as $role) {
            if($role->name === 'admin' && !parent::isAllowed($this->perms->addAdmin)) {
                continue;
            }
            $options[$role->id] = $role->name;
        }

        $form->addSelect('role', 'Role',$options);

        $form->addSubmit('send', 'Vytvořit');

        $form->onSuccess[] = [$this, 'addUserFormSucceeded'];
        return $form;
    }

    /**
     * @param array<string,string> $data
     */
    public function addUserFormSucceeded(Form $form, array $data): void
    {

        $newUser = UserDTO::create(
            id: null,
            name: $data["name"],
            role: (int)$data["role"],
            password: $this->passwords->hash($data["password"]),
            resolvedRole: $this->roleFacade->getDTOById((int)$data["role"]),
        );

        try {
            $this->userFacade->insertDTO($newUser);
            $this->flashMessage("Uživatel ".$newUser->name." úspěšně přidán", 'success');
        } catch (Exception $e) {
            $form->addError('Nastala chyba při pridávání.');
            $this->flashMessage($e->getMessage(), 'error');
        }
        $this->redirect('Admin:');
    }

    public function createComponentEditUserForm() {

        $form = new Form;
        $form->addText('name', 'Uživatelské jméno:');

        $form->addPassword('password', 'Heslo:');

        $this->roleFacade->getAllDTO();
        $options = [];
        foreach ($this->roleFacade->getAllDTO() as $role) {
            if($role->name === 'admin' && !parent::isAllowed($this->perms->addAdmin)) {
                continue;
            }
            $options[$role->id] = $role->name;
        }

        $form->addSelect('role_id', 'Role',$options);

        $form->addSubmit('send', 'Upravit');

        $form->onSuccess[] = [$this, 'editUserFormSucceeded'];
        return $form;
    }

    public function editUserFormSucceeded(Form $form, array $data): void
    {
        $id = $this->getParameter("id");

        $newUser = UserDTO::create(
            id: intval($id),
            name: $data["name"],
            role: (int)$data["role_id"],
            password:
                empty($data["password"])?
                $this->userFacade->getDTOById(intval($id))->password
                :$this->passwords->hash($data["password"]),
            resolvedRole: $this->roleFacade->getDTOById((int)$data["role_id"]),
        );

        try {
            $this->userFacade->updateDTO($newUser);
            $this->flashMessage("Uživatel ".$newUser->name." úspěšně upraven", 'success');
        } catch (Exception $e) {
            $form->addError('Nastala chyba při upravování.');
            $this->flashMessage($e->getMessage(), 'error');
        }
        $this->redirect('Admin:editUser', ['id' => $id]);
    }

    public function renderUserList()
    {
        $this->template->users = $this->userFacade->getAllDTO();
    }

    public function renderEditUser(int $id)
    {
        $userDTO = $this->userFacade->getDTOById($id);
        $this->template->userDTO = $userDTO;

        $this->getComponent('editUserForm')
            ->setDefaults($userDTO->toArray());
    }

    public function renderRoleList()
    {
        $rolePairs = [];
        foreach ($this->roleFacade->getAllDTO() as $role) {
            $rolePairs[] = [$role,$this->rolePermissionFacade->getRolePermissionsByRoleId($role->id)];
        }
        $this->template->roles = $rolePairs;
    }

    public function renderEditRole(int $id)
    {
        $roleDTO = $this->roleFacade->getDTOById($id);
        $this->template->roleDTO = $roleDTO;
    }

    public function createComponentEditRoleForm() {
        $roleId = $this->getParameter("id");

        $form = $this->roleEditFormFactory->create($this, intval($roleId));

        $form->addSubmit('send', 'Upravit');

        $form->onSuccess[] = [$this, 'editRoleFormSucceeded'];
        return $form;
    }

    public function editRoleFormSucceeded(Form $form, array $data): void
    {
        $roleId = $this->getParameter("id");

        $this->editRole(intval($roleId), $data);
    }

    public function createComponentAddRoleForm() {
        $form = $this->roleEditFormFactory->create($this, null);
        $form->addText("name","name")->setRequired();
        $form->addSubmit('send', 'Přidat');

        $form->onSuccess[] = [$this, 'addRoleFormSucceeded'];

        return $form;
    }

    public function addRoleFormSucceeded(Form $form, array $data): void
    {
        $roleDTO = RoleDTO::create(
            id: null,
            name: $data["name"],
        );
        $roleId = $this->roleFacade->insertDTO($roleDTO)->id;

        $this->editRole(intval($roleId), $data);
    }

    public function editRole(int $roleId, array $data): void {

        $roleDTO = $this->roleFacade->getDTOById($roleId);

        try {
            $this->rolePermissionFacade->updateRolePermissions($roleDTO, $data);
            $this->flashMessage("Oprávnění pro roli úspěšně upraveno", 'success');
        }
        catch(\Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
        $this->redirect('Admin:editRole', ['id' => strval($roleId)]);
    }

    public function renderAddRole()
    {

    }
}