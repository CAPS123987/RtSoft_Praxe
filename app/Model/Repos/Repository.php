<?php

namespace App\Model\Repos;

use App\Model\Mapper\Mapper;
use Nette;
use Nette\Database\Table\ActiveRow;
use \App\Model\DTOs\DTO;

abstract class Repository
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    abstract function getTableName(): string;

    public function getById(int $id): ActiveRow|null
    {
        return $this->database->table($this->getTableName())->get($id);
    }
    public function getAll(): Nette\Database\Table\Selection
    {
        return $this->database->table($this->getTableName());
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string, mixed>|bool|int|ActiveRow
     */
    public function insert(array $data): array|bool|int|ActiveRow
    {
        $unionArray = $this->columnUnion($data);

        return $this->getAll()->insert($unionArray);
    }

    /**
     * @param array<string,mixed> $data
    */
    public function update(int $id, array $data): int
    {
        $unionArray = $this->columnUnion($data);

        return $this->getAll()->where('id', $id)->update($unionArray);
    }

    /**
     * @param array<string,mixed> $data
     * @return int Returns the ID of the inserted or updated record
     */
    public function save(array $data): int
    {
        if(array_key_exists('id', $data)) {
            /** @var int|null $potentialId */
            $potentialId = $data['id'];
            if(!empty($potentialId) && $this->idExists($potentialId)) {
                /** @var int $id */
                $id = $data['id'];

                $this->update($id, $data);
                return $id;
            }
        }
        /** @var ActiveRow $row */
        $row = $this->insert($data);
        /** @var int $rowId */
        $rowId = $row->id;
        return $rowId;
    }

     public function delete(int $id): int
     {
         return $this->getAll()->where('id', $id)->delete();
     }

     public function idExists(int $id): bool
     {
        return (bool) $this->getById($id);
     }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
     public function columnUnion(array $data): array
     {
         $dbColumnKeys = array_flip($this->getColumns());
         return array_intersect_key($data, $dbColumnKeys);
     }

    /**
     * @return array<int,string> $data
     */
     public function getColumns(): array
     {
         $columns = [];

         foreach ($this->database->getStructure()->getColumns($this->getTableName()) as $columnName => $table) {
             $columns[] = $table["name"];
         }

         return $columns;
     }
}