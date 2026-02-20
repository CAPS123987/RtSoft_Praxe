<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;


use App\Model\Permission\PermissionList;
use App\Model\Post\Facades\PostFacade;


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
