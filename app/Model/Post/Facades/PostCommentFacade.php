<?php

namespace App\Model\Post\Facades;

use App\Model\Comment\DTO\CommentDTO;
use App\Model\Post\DTO\PostDTO;

final class PostCommentFacade
{
    public function __construct(
        private readonly \App\Model\Post\Facades\PostFacade       $postFacade,
        private readonly \App\Model\Comment\Facades\CommentFacade $commentFacade,
    ) {
    }

    public function getPostsComments(PostDTO $post): array
    {
        return $this->commentFacade->getCommentsByPostId($post->id);
    }

    public function getPostByComment(CommentDTO $commentDTO): PostDTO
    {
        return $this->postFacade->getDTOById($commentDTO->post_id);
    }
}