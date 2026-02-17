<?php

namespace App\Model\Facades;

use App\Model\DTOs\CommentDTO;
use App\Model\DTOs\DTO;
use App\Model\DTOs\PostDTO;
use App\Model\Mapper\Mapper;
use Nette;
use App\Model\Facades;

final class PostCommentFacade
{
    public function __construct(
        private readonly Facades\PostFacade    $postFacade,
        private readonly Facades\CommentFacade $commentFacade,
    ) {
    }

    public function getPostsComments(PostDTO $post): array
    {
        return $this->commentFacade->getCommentsByPostId($post->id);
    }
}