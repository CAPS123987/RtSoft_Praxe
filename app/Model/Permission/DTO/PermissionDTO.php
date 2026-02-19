<?php

namespace App\Model\Permission\DTO;

use App\Model\Generics\DTO\DTO;
use App\Model\Permission\Repo\PermissionRepository;

class PermissionDTO implements DTO
{
    private function __construct(
        public readonly ?int    $id,
        public readonly string  $name = '',
    ) {
    }

    public static function create(?int $id, string $name): self
    {
        return new self($id, $name);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data[PermissionRepository::ID_COL] ?? null,
            name: $data[PermissionRepository::NAME_COL] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            PermissionRepository::ID_COL => $this->id,
            PermissionRepository::NAME_COL => $this->name,
        ];
    }
}

