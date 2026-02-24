<?php

namespace App\Model\Like\Facades;

use App\Model\Like\DTO\PostLikeDTO;
use App\Model\Like\Repo\PostLikeRepository;

final class PostLikeFacade
{
    public function __construct(
        private readonly PostLikeRepository $postLikeRepository,
    ) {
    }

    /**
     * Toggle like — pokud uživatel již likoval, odebere like; jinak přidá.
     * @return bool true pokud byl like přidán, false pokud odebrán
     */
    public function toggleLike(int $postId, int $userId): bool
    {
        $existing = $this->postLikeRepository->findByPostAndUser($postId, $userId);

        if ($existing !== null) {
            $this->postLikeRepository->deleteByPostAndUser($postId, $userId);
            return false;
        }

        $dto = PostLikeDTO::create(null, $postId, $userId);
        $this->postLikeRepository->insert($dto->toArray());
        return true;
    }

    /**
     * Vrátí počet liků pro daný příspěvek.
     */
    public function getLikeCount(int $postId): int
    {
        return $this->postLikeRepository->countByPost($postId);
    }

    /**
     * Zjistí, zda uživatel lajkoval daný příspěvek.
     */
    public function hasUserLiked(int $postId, int $userId): bool
    {
        return $this->postLikeRepository->findByPostAndUser($postId, $userId) !== null;
    }
}

