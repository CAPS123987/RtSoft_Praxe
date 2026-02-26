<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;


use App\Model\Permission\PermissionList;
use App\Model\Post\Facades\PostFacade;


final class HomepagePresenter extends BasePresenter
{
    /** @persistent */
    public int $page = 1;

    public function __construct(
        private PostFacade $facade,
        PermissionList $perms,
    ) {
        parent::__construct($perms);
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->facade
            ->getPublicArticlesPage($this->page);

        $this->template->page = $this->page;
    }

    public function handleNextPage(): void
    {
        $this->page++;

        if ($this->isAjax()) {
            $this->redrawControl('postsSnipet');
            $this->redrawControl('previousPageSnipet');
            $this->redrawControl('nextPageSnipet');
        } else {
            $this->redirect('this');
        }
    }

    public function handlePreviousPage(): void
    {
        if ($this->page <= 1) {
            return;
        }

        $this->page--;

        if ($this->isAjax()) {
            $this->redrawControl('postsSnipet');
            $this->redrawControl('previousPageSnipet');
            $this->redrawControl('nextPageSnipet');
        } else {
            $this->redirect('this');
        }
    }
}
