<?php

namespace App\Model\Post\Facades;

use App;
use App\Model\Generics\Facades\DTOFacade;
use App\Model\Post\DTO\postDTO;

/**
 * @extends DTOFacade<PostDTO>
 */
final class PostFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Post\Repo\PostRepository $postRepository,
        private readonly App\Model\Post\Mapper\PostMapper   $postMapper,
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

}