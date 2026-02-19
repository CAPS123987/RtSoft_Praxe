<?php

namespace App\Model\Comment\Facades;

use App\Model\Comment\DTO\CommentDTO;
use App\Model\Repos;
use Nette;

final class CommentDeletionFacade
{
    public function __construct(
        private readonly \App\Model\Deletion\Repo\DeletionRepository $deletionRepository,
        private readonly \App\Model\Comment\Repo\CommentRepository   $commentRepository,
        private readonly Nette\Database\Explorer                     $database,
    ) {
    }

    public function deleteCommentTransaction(int $id): bool {
        $this->database->beginTransaction();
        try {
            $this->deleteComment($id);
            $this->database->commit();
            return true;
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            return false;
        }
    }

    public function deleteComment(int $id): void
    {
        $comment = $this->commentRepository->getById($id);
        if ($comment === null) {
            throw new \RuntimeException("Comment with id {$id} not found");
        }
        $this->deletionRepository->logDeletion(\App\Model\Comment\Repo\CommentRepository::TABLE_NAME, $comment->toArray());
        $this->commentRepository->delete($id);
    }

    public function deleteCommentDTOTransaction(CommentDTO $commentDTO): bool
    {
        if ($commentDTO->id === null) {
            throw new \InvalidArgumentException("CommentDTO must have an id");
        }
        return $this->deleteCommentTransaction($commentDTO->id);
    }

    public function deleteCommentsByPostIdTransaction(int $postId): bool {
        $this->database->beginTransaction();
        try {
            $this->deleteCommentsByPostId($postId);
            $this->database->commit();
            return true;
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            return false;
        }
    }

    public function deleteCommentsByPostId(int $postId): void
    {
        $comments = $this->commentRepository->getCommentsByPostId($postId);
        foreach ($comments as $comment) {
            $this->deletionRepository->logDeletion(\App\Model\Comment\Repo\CommentRepository::TABLE_NAME, $comment->toArray());
        }
        $comments->delete();
    }
}