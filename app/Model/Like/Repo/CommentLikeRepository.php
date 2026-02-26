<?php

namespace App\Model\Like\Repo;

use App\Model\Generics\Repo\Repository;
use Nette;

final class CommentLikeRepository extends Repository
{
    public const string TABLE_NAME = "comment_likes";
    public const string ID_COL = "id";
    public const string COMMENT_ID_COL = "comment_id";
    public const string USER_ID_COL = "user_id";

    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct($this->database);
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    public function findByCommentAndUser(int $commentId, int $userId): ?Nette\Database\Table\ActiveRow
    {
        return $this->getAll()
            ->where(self::COMMENT_ID_COL, $commentId)
            ->where(self::USER_ID_COL, $userId)
            ->fetch() ?: null;
    }

    public function countByComment(int $commentId): int
    {
        return $this->getAll()
            ->where(self::COMMENT_ID_COL, $commentId)
            ->count('*');
    }

    public function deleteByCommentAndUser(int $commentId, int $userId): int
    {
        return $this->getAll()
            ->where(self::COMMENT_ID_COL, $commentId)
            ->where(self::USER_ID_COL, $userId)
            ->delete();
    }

    public function deleteByUserId(int $userId): int
    {
        return $this->getAll()
            ->where(self::USER_ID_COL, $userId)
            ->delete();
    }

    public function deleteByCommentId(int $commentId): int
    {
        return $this->getAll()
            ->where(self::COMMENT_ID_COL, $commentId)
            ->delete();
    }
}

