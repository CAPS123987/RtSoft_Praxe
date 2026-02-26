<?php

namespace App\Model\User\Facades;

use App\Model\Comment\Facades\CommentDeletionFacade;
use App\Model\Deletion\Repo\DeletionRepository;
use App\Model\Like\Repo\CommentLikeRepository;
use App\Model\Like\Repo\PostLikeRepository;
use App\Model\Post\Facades\PostFacade;
use App\Model\Post\Repo\PostRepository;
use App\Model\User\Repo\UserRepository;
use Nette;

final class UserDeletionFacade
{
    public function __construct(
        private readonly DeletionRepository      $deletionRepository,
        private readonly CommentDeletionFacade    $commentDeletionFacade,
        private readonly PostLikeRepository       $postLikeRepository,
        private readonly CommentLikeRepository    $commentLikeRepository,
        private readonly UserFacade               $userFacade,
        private readonly PostFacade               $postFacade,
        private readonly PostRepository           $postRepository,
        private readonly Nette\Database\Explorer  $database,
    ) {
    }

    /**
     * Smaže uživatele včetně všech jeho relací:
     * 1. Smaže komentáře uživatele (včetně jejich liků)
     * 2. Smaže liky uživatele (post likes i comment likes)
     * 3. Smaže posty uživatele (a jejich komentáře a liky)
     * 4. Zaloguje smazání a smaže samotného uživatele
     */
    public function deleteUser(int $userId): bool
    {
        $this->database->beginTransaction();
        try {
            $user = $this->userFacade->getDTOById($userId);

            // 1. Smazat komentáře, které uživatel napsal (včetně comment likes)
            $this->commentDeletionFacade->deleteCommentsByOwnerId($userId);

            // 2. Smazat liky uživatele (post likes i comment likes)
            $this->postLikeRepository->deleteByUserId($userId);
            $this->commentLikeRepository->deleteByUserId($userId);

            // 3. Smazat posty uživatele (kaskádově smaže i komentáře a liky k těm postům)
            $posts = $this->postRepository->getPostsByOwnerId($userId);
            foreach ($posts as $post) {
                // Smazat komentáře u postu
                $this->commentDeletionFacade->deleteCommentsByPostId($post->id);
                // Zalogovat smazání postu
                $postDTO = $this->postFacade->getDTOById($post->id);
                $this->deletionRepository->logDeletion(PostRepository::TABLE_NAME, $postDTO->toArray());
                // Smazat post
                $this->postFacade->deleteDTO($postDTO);
            }

            // 4. Zalogovat smazání uživatele a smazat ho
            $this->deletionRepository->logDeletion(UserRepository::TABLE_NAME, $user->toArray());
            $this->userFacade->deleteDTO($user);

            $this->database->commit();
            return true;
        } catch (\Throwable $exception) {
            $this->database->rollBack();
            return false;
        }
    }
}
