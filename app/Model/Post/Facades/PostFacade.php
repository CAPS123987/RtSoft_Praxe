<?php

namespace App\Model\Post\Facades;

use App;
use App\Model\Generics\Facades\DTOFacade;
use App\Model\Post\DTO\PostDTO;

/**
 * @extends DTOFacade<PostDTO>
 */
final class PostFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Post\Repo\PostRepository $postRepository,
        private readonly App\Model\Post\Mapper\PostMapper   $postMapper,
        private readonly App\Model\Post\PostPaginator $postPaginator,
    ) {
        parent::__construct($postRepository, $postMapper);
    }

    /**
     * @return array<PostDTO>
     */
    public function getPublicArticles(): array
    {
        return $this->postMapper->mapAll($this->postRepository->getPublicArticles());
    }

    public function getPublicArticlesPage(int $page): array
    {
        $selection = $this->postRepository->getPublicArticles();
        $paginatedSelection = $this->postPaginator->addPaginationToQuery($selection, $page);
        return $this->postMapper->mapAll($paginatedSelection);
    }

}