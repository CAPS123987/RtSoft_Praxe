<?php

namespace App\Model\Post\Facades;

use App\Model\Post\DTO\PostDTO;
use App\Model\Repos;
use Nette;

final class PostDeletionFacade
{
    public function __construct(
        private readonly \App\Model\Deletion\Repo\DeletionRepository      $deletionRepository,
        private readonly \App\Model\Comment\Facades\CommentDeletionFacade $commentFacade,
        private readonly \App\Model\Post\Facades\PostFacade               $postFacade,
        private readonly Nette\Database\Explorer                          $database,
    ) {
    }

    public function deletePost(int $id): bool {
        $this->database->beginTransaction();
        try {
            $post = $this->postFacade->getDTOById($id);
            $this->deletionRepository->logDeletion(\App\Model\Post\Repo\PostRepository::TABLE_NAME, $post->toArray());
            $this->commentFacade->deleteCommentsByPostId($id);
            $this->postFacade->deleteDTO($post);
            $this->database->commit();
            return true;
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            return false;
        }
    }

    public function deletePostDTO(PostDTO $postDTO): bool
    {
        return $this->deletePost($postDTO->id);
    }
}