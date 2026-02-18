<?php

namespace App\Model\User\Repo;

use App\Model\Generics\Repo\Repository;
use Nette;

final class UserRepository extends Repository
{
    public const string TABLE_NAME = "users";
    public const string ID_COL = "id";
    public const string ROLE_COL = "role_id";
    public const string NAME_COL = "name";
    public const string PASSWORD_COL = "password_hash";


    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct($this->database);
    }

    function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}