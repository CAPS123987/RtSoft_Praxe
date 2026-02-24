<?php

namespace App\Model\Like\Repo;

use App\Model\Generics\Repo\Repository;
use Nette;

final class PostLikeRepository extends Repository
{
    public const string TABLE_NAME = "post_likes";
    public const string ID_COL = "id";
    public const string POST_ID_COL = "post_id";
    public const string USER_ID_COL = "user_id";
    public const string CREATED_AT_COL = "created_at";

    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct($this->database);
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function findByPostAndUser(int $postId, int $userId): ?Nette\Database\Table\ActiveRow
    {
        return $this->getAll()
            ->where(self::POST_ID_COL, $postId)
            ->where(self::USER_ID_COL, $userId)
            ->fetch() ?: null;
    }

    public function countByPost(int $postId): int
    {
        return $this->getAll()
            ->where(self::POST_ID_COL, $postId)
            ->count('*');
    }

    public function deleteByPostAndUser(int $postId, int $userId): int
    {
        return $this->getAll()
            ->where(self::POST_ID_COL, $postId)
            ->where(self::USER_ID_COL, $userId)
            ->delete();
    }
}

