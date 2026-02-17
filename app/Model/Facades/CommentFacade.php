<?php

namespace App\Model\Facades;

use App\Model\DTOs\CommentDTO;
use App\Model\DTOs\PostDTO;
use Nette\Database\Table\ActiveRow;
use App;
use App\Model\DTOs\DTO;

/**
 * @extends DTOFacade<CommentDTO>
 */
final class CommentFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Repos\CommentRepository $commentRepository,
        private readonly App\Model\Mapper\Mapper $mapper,
    ) {
        parent::__construct($commentRepository, $mapper);
    }


    /**
     * @param int $postId
     * @return array<int, CommentDTO>
     */
    public function getCommentsByPostId(int $postId): array
    {
        return $this->mapper->mapAll(
            $this->commentRepository->getCommentsByPostId($postId)
        );
    }

}