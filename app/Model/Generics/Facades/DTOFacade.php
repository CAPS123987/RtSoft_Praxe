<?php

namespace App\Model\Generics\Facades;

use App;
use App\Model\Generics\DTO\DTO;
use Nette\Database\Table\ActiveRow;


/**
 * @template T of DTO
 */
abstract class DTOFacade {
    public function __construct(
        private readonly App\Model\Generics\Repo\Repository $repository,
        private readonly App\Model\Generics\Mapper\Mapper   $mapper,
    ) {
    }


    /**
     * @return DTO
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
     * @return DTO
     * @throws \RuntimeException
     */
    public function getDTOByX(int $X, string $value): DTO
    {
        $row = $this->repository->getByX($X, $value);
        if (!$row) {
            throw new \RuntimeException('Record not found');
        }
        return $this->mapper->map($row);
    }

    /**
     * @param DTO $DTO
     * @return void
     */
    public function insertDTO(DTO $DTO): ActiveRow
    {
        /** @var ActiveRow $row */
        $row = $this->repository->insert($DTO->toArray());
        return $row;
    }

    /**
     * @param DTO $DTO
     *@throws \RuntimeException
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