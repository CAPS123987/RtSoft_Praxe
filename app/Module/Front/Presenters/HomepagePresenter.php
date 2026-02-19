<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;


use App\Model\Permission\PermissionList;
use App\Model\Permission\Repo\PermissionRepository;
use App\Model\Post\Facades\PostFacade;
use App\Model\User\Auth\Facades\RolePermissionFacade;
use App;
use Nette;


final class HomepagePresenter extends BasePresenter
{
    public function __construct(
        private PostFacade $facade,
        private PermissionList $perms,
    ) {
        parent::__construct($perms);
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->facade
            ->getPublicArticles();

    }
}
