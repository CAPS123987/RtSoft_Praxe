<?php

namespace App\Model\Like\Facades;

use App\Model\Like\DTO\CommentLikeDTO;
use App\Model\Like\Repo\CommentLikeRepository;

final class CommentLikeFacade
{
    public function __construct(
        private readonly CommentLikeRepository $commentLikeRepository,
    ) {
    }

    /**
     * Toggle like — pokud uživatel již likoval komentář, odebere like; jinak přidá.
     * @return bool true pokud byl like přidán, false pokud odebrán
     */
    public function toggleLike(int $commentId, int $userId): bool
    {
        $existing = $this->commentLikeRepository->findByCommentAndUser($commentId, $userId);

        if ($existing !== null) {
            $this->commentLikeRepository->deleteByCommentAndUser($commentId, $userId);
            return false;
        }

        $dto = CommentLikeDTO::create(null, $commentId, $userId);
        $this->commentLikeRepository->insert($dto->toArray());
        return true;
    }

    /**
     * Vrátí počet liků pro daný komentář.
     */
    public function getLikeCount(int $commentId): int
    {
        return $this->commentLikeRepository->countByComment($commentId);
    }

    /**
     * Zjistí, zda uživatel lajkoval daný komentář.
     */
    public function hasUserLiked(int $commentId, int $userId): bool
    {
        return $this->commentLikeRepository->findByCommentAndUser($commentId, $userId) !== null;
    }
}

