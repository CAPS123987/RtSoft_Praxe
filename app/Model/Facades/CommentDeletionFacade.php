<?php

namespace App\Model\Facades;

use App\Model\DTOs\CommentDTO;
use Nette;
use App\Model\Repos;

final class CommentDeletionFacade
{
    public function __construct(
        private readonly Repos\DeletionRepository $deletionRepository,
        private readonly Repos\CommentRepository $commentRepository,
        private readonly Nette\Database\Explorer $database,
    ) {
    }

    public function deleteComment(int $id): bool {
        $this->database->beginTransaction();
        try {
            $comment = $this->commentRepository->getById($id)->toArray();
            $this->deletionRepository->logDeletion($id, $comment);
            $this->commentRepository->delete($id);
            $this->database->commit();
            return true;
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            return false;
        }
    }

    public function deleteCommentDTO(CommentDTO $commentDTO): bool
    {
        return $this->deleteComment($commentDTO->id);
    }
}