<?php

namespace App\Model\Permission\Repo;

use App\Model\Generics\Repo\Repository;
use Nette;

final class PermissionRepository extends Repository
{
    public const string TABLE_NAME = "permissions";
    public const string ID_COL = "id";
    public const string NAME_COL = "name";

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

