<?php

namespace App\Model\RepoConnections;

use App\Model\Generics\Repo\Repository;
use Nette;

final class PermissionRoleRepository extends Repository
{
    public const string TABLE_NAME = "permissionsRoles";
    public const string ID_COL = "id";
    public const string PERMISSION_ID_COL = "permission_id";
    public const string ROLE_ID_COL = "role_id";

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

