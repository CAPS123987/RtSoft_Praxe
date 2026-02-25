<?php

namespace App\Model\Comment\Repo;

use App\Model\Generics\Repo\Repository;
use Nette;

final class CommentRepository extends Repository
{
    public const string TABLE_NAME = "comments";
    public const string ID_COL = "id";
    public const string POST_ID_COL = "post_id";
    public const string OWNER_COL = "owner_id";
    public const string NAME_COL = "name";
    public const string EMAIL_COL = "email";
    public const string CONTENT_COL = "content";
    public const string CREATED_AT_COL = "created_at";


    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct($this->database);
    }

    public function getPublicComments(): Nette\Database\Table\Selection
    {
        return $this->getAll()
            ->where(self::CREATED_AT_COL . ' < ', new \DateTime)
            ->order(self::CREATED_AT_COL . ' DESC');

    }

    function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    function getPostIdByCommentId(int $commentId): ?int
    {
        $comment = $this->getById($commentId);
        return $comment?->{self::POST_ID_COL};
    }

    function getCommentsByPostId(int $postId): Nette\Database\Table\Selection
    {
        return $this->getAll()->where(self::POST_ID_COL . ' = ?', $postId);
    }

    function getCommentsByOwnerId(int $ownerId): Nette\Database\Table\Selection
    {
        return $this->getAll()->where(self::OWNER_COL . ' = ?', $ownerId);
    }
}