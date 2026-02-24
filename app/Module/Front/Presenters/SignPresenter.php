<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model\Permission\PermissionList;
use App\Module\Front\Components\SignIn\SignInComponent;
use App\Module\Front\Components\SignIn\SignInComponentFactory;

class SignPresenter extends BasePresenter
{
    public function __construct(
        PermissionList $perms,
        private SignInComponentFactory $signInComponentFactory,
    ) {
        parent::__construct($perms);
    }

    public function actionOut(): void
    {
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.');
        $this->redirect('Homepage:');
    }

    protected function createComponentSignInForm(): SignInComponent
    {
        return $this->signInComponentFactory->create();
    }
}