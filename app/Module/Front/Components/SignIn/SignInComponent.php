<?php

namespace App\Module\Front\Components\SignIn;

use Nette;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class SignInComponent extends Control
{
    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/SignInComponent.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }

    /**
     * @param array<string,string> $data
     */
    public function signInFormSucceeded(Form $form, array $data): void
    {
        $presenter = $this->getPresenter();

        try {
            $presenter->getUser()->login($data["username"], $data["password"]);
            $presenter->redirect('Homepage:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }
}

