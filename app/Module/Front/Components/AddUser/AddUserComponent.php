<?php

namespace App\Module\Front\Components\AddUser;

use App\Model\Permission\PermissionList;
use App\Model\Role\Facades\RoleFacade;
use App\Model\User\DTO\UserDTO;
use App\Model\User\Facades\UserFacade;
use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class AddUserComponent extends Control
{
    public function __construct(
        private readonly Nette\Security\Passwords $passwords,
        private readonly PermissionList           $perms,
        private readonly RoleFacade               $roleFacade,
        private readonly UserFacade               $userFacade,
    )
    {
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/AddUserComponent.latte');
        $this->template->render();
    }

    public function createComponentForm(): Form {

        $form = new Form;
        $form->addText('name', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte heslo.');

        $canAddAdmin = $this->getPresenter()->isAllowed($this->perms->addAdmin);
        $options = $this->roleFacade->getFilteredRoleOptions($canAddAdmin);

        $form->addSelect('role', 'Role', $options);

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
            $this->getPresenter()->flashMessage("Uživatel ".$newUser->name." úspěšně přidán", 'success');
        } catch (\Exception $e) {
            $form->addError('Nastala chyba při pridávání.');
            $this->getPresenter()->flashMessage($e->getMessage(), 'error');
        }
        $this->getPresenter()->redirect('Admin:');
    }


}