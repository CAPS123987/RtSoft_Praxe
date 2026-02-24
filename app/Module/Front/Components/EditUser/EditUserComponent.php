<?php

namespace App\Module\Front\Components\EditUser;

use App\Model\Permission\PermissionList;
use App\Model\Role\Facades\RoleFacade;
use App\Model\User\DTO\UserDTO;
use App\Model\User\Facades\UserFacade;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;

class EditUserComponent extends Control
{
    public function __construct(
        private readonly Passwords $passwords,
        private readonly PermissionList $perms,
        private readonly RoleFacade $roleFacade,
        private readonly UserFacade $userFacade,
        private readonly int $userId,
    )
    {
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/EditUserComponent.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;
        $form->addText('name', 'Uživatelské jméno:');

        $form->addPassword('password', 'Heslo:');

        $userDTO = $this->userFacade->getDTOById($this->userId);
        $canAddAdmin = $this->getPresenter()->isAllowed($this->perms->addAdmin);
        $options = $this->roleFacade->getFilteredRoleOptions($canAddAdmin, $userDTO->role);

        $form->addSelect('role_id', 'Role', $options);

        $form->addSubmit('send', 'Upravit');

        $form->onSuccess[] = [$this, 'editUserFormSucceeded'];

        $form->setDefaults($userDTO->toArray());

        return $form;
    }

    /**
     * @param array<string,string> $data
     */
    public function editUserFormSucceeded(Form $form, array $data): void
    {
        $newUser = UserDTO::create(
            id: $this->userId,
            name: $data["name"],
            role: (int)$data["role_id"],
            password:
                empty($data["password"]) ?
                    $this->userFacade->getDTOById($this->userId)->password
                    : $this->passwords->hash($data["password"]),
            resolvedRole: $this->roleFacade->getDTOById((int)$data["role_id"]),
        );

        try {
            $this->userFacade->updateDTO($newUser);
            $this->getPresenter()->flashMessage("Uživatel " . $newUser->name . " úspěšně upraven", 'success');
        } catch (\Exception $e) {
            $form->addError('Nastala chyba při upravování.');
            $this->getPresenter()->flashMessage($e->getMessage(), 'error');
        }
        $this->getPresenter()->redirect('Admin:editUser', ['id' => $this->userId]);
    }
}

