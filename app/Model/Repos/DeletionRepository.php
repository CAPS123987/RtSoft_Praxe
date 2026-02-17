<?php

namespace App\Model\Repos;

use Nette;

final class DeletionRepository extends Repository
{
    public const string TABLE_NAME = "deletions";
    public const string ID_COL = "id";
    public const string TIME_COL = "time";
    public const string TABLE_NAME_COL = "table_name";
    public const string DATA_COL = "data";
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
        parent::__construct($this->database);
    }

    function getTableName(): string
    {
        return self::TABLE_NAME;
    }

    /**
     * @param string $tableName
     * @param array<string,mixed> $data
     * @return void
     */
    function logDeletion(string $tableName, array $data): void
    {
        $jsonData = json_encode($data);
        $this->insert([self::TABLE_NAME_COL => $tableName, self::DATA_COL => $jsonData]);
    }
}