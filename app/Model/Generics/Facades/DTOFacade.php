<?php

namespace App\Model\Generics\Facades;

use App;
use App\Model\Generics\DTO\DTO;
use Nette\Database\Table\ActiveRow;


/**
 * @template T of DTO
 */
abstract class DTOFacade {
    /**
     * @param App\Model\Generics\Mapper\Mapper<T> $mapper
     */
    public function __construct(
        private readonly App\Model\Generics\Repo\Repository $repository,
        private readonly App\Model\Generics\Mapper\Mapper   $mapper,
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
     * @return T
     * @throws \RuntimeException
     */
    public function getDTOByX(string $X, string $value): DTO
    {
        $row = $this->repository->getByX($X, $value);
        if (!$row) {
            throw new \RuntimeException('Record not found');
        }
        return $this->mapper->map($row);
    }

    /**
     * @param T $DTO
     */
    public function insertDTO(DTO $DTO): ActiveRow
    {
        /** @var ActiveRow $row */
        $row = $this->repository->insert($DTO->toArray());
        return $row;
    }

    /**
     * @param T $DTO
     *@throws \RuntimeException
     */
    public function updateDTO(DTO $DTO): void
    {
        if (!isset($DTO->id) || $DTO->id === null) {
            throw new \RuntimeException('Record ID is required for update');
        }
        $this->repository->update($DTO->id, $DTO->toArray());
    }

    /**
     * @param T $DTO
     * @return int
     */
    public function deleteDTO(DTO $DTO): int
    {
        if (!isset($DTO->id) || $DTO->id === null) {
            throw new \RuntimeException('Record ID is required for deletion');
        }
        return $this->repository->delete($DTO->id);
    }

    /**
     * @param T $DTO
     * @return int
     */
    public function saveDTO(DTO $DTO): int
    {
        if (!isset($DTO->id)) {
            throw new \RuntimeException('DTO must have id property');
        }
        return $this->repository->save($DTO->toArray());
    }
}