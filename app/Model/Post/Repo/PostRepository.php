<?php

namespace App\Model\Post\Repo;

use App\Model\Generics\Repo\Repository;
use Nette;

final class PostRepository extends Repository
{
    public const string TABLE_NAME = "posts";
    public const string ID_COL = "id";
    public const string TITLE_COL = "title";
    public const string CONTENT_COL = "content";
    public const string CREATED_AT_COL = "created_at";
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct($this->database);
    }

    public function getPublicArticles(): Nette\Database\Table\Selection
    {
        return $this->getAll()
            ->where(self::CREATED_AT_COL . ' < ', new \DateTime)
            ->order(self::CREATED_AT_COL . ' DESC');
    }

    function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}