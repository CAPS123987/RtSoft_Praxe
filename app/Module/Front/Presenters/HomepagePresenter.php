<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;


use App\Model\Repos\PostRepository;
use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private PostRepository $facade,
    ) {
    }

    public function renderDefault(): void
    {
        $this->template->posts = $this->facade
            ->getPublicArticles()
            ->limit(5);
    }
}
