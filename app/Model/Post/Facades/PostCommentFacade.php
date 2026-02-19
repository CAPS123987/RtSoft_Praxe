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

    /**
     * @return array<CommentDTO>
     */
    public function getPostsComments(PostDTO $post): array
    {
        if ($post->id === null) {
            throw new \InvalidArgumentException("PostDTO must have an id");
        }
        return $this->commentFacade->getCommentsByPostId($post->id);
    }

    public function getPostByComment(CommentDTO $commentDTO): PostDTO
    {
        $post = $this->postFacade->getDTOById($commentDTO->post_id);
        if (!$post instanceof PostDTO) {
            throw new \RuntimeException("Expected PostDTO, got different type");
        }
        return $post;
    }
}