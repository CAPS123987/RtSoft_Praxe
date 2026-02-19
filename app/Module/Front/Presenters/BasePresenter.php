<?php

namespace App\Module\Front\Presenters;

use App\Model\Permission\PermissionList;
use App\Model\Post\Facades\PostFacade;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    public function __construct(
        private PermissionList $permList,
    ) {
    }
    public function beforeRender() {
        $this->template->identity = $this->getUser()->getIdentity();
        $this->template->permList = $this->permList;
    }


    public function isAllowed(string $permission): bool
    {
        if(!$this->getUser()->isLoggedIn()) {
            return false;
        }
        $identity = $this->getUser()->getIdentity();
        if ($identity === null) {
            return false;
        }
        return $identity->hasPermission($permission);
    }
}