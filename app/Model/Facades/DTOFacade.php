<?php

namespace App\Model\Facades;

use App;
use App\Model\DTOs\DTO;
use Nette\Database\Table\ActiveRow;


/**
 * @template T of DTO
 */
abstract class DTOFacade {
    public function __construct(
        private readonly App\Model\Repos\Repository $repository,
        private readonly App\Model\Mapper\Mapper    $mapper,
    ) {
    }


    /**
     * @return T
     * @throws \RuntimeException
     */
    public function getDTOById(int $id): DTO
    {
        $row = $this->repository->getById($id);
        if (!$row) {
            throw new \RuntimeException('Record not found');
        }
        return $this->mapper->map($row);
    }

    /**
     * @param T $DTO
     * @return void
     */
    public function insertDTO(DTO $DTO): ActiveRow
    {
        /** @var ActiveRow $row */
        $row = $this->repository->insert($DTO->toArray());
        return $row;
    }

    /**
     * @throws \RuntimeException
     * @param T $DTO
     */
    public function updateDTO(DTO $DTO): void
    {
        if (!$DTO->id) {
            throw new \RuntimeException('Record ID is required for update');
        }
        $this->repository->update($DTO->id, $DTO->toArray());
    }

    public function deleteDTO(DTO $DTO): bool
    {
        if (!$DTO->id) {
            throw new \RuntimeException('Record ID is required for deletion');
        }
        return $this->repository->delete($DTO->id);
    }

    public function saveDTO(DTO $DTO): int
    {
        return $this->repository->save($DTO->toArray());
    }
}