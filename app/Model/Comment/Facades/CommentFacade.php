<?php

namespace App\Model\Comment\Facades;

use App;
use App\Model\Comment\DTO\CommentDTO;
use App\Model\Generics\Facades\DTOFacade;

/**
 * @extends DTOFacade<CommentDTO>
 */
final class CommentFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Comment\Repo\CommentRepository $commentRepository,
        private readonly App\Model\Comment\Mapper\CommentMapper   $commentMapper,
    ) {
        parent::__construct($commentRepository, $commentMapper);
    }


    /**
     * @param int $postId
     * @return array<int, CommentDTO>
     */
    public function getCommentsByPostId(int $postId): array
    {
        return $this->commentMapper->mapAll(
            $this->commentRepository->getCommentsByPostId($postId)
        );
    }

}