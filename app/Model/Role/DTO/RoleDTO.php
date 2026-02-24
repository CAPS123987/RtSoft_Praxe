<?php

namespace App\Model\Role\DTO;

use App\Model\Generics\DTO\DTO;
use App\Model\Role\Repo\RoleRepository;

class RoleDTO implements DTO
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
        /** @var int|null $id */
        $id = $data[RoleRepository::ID_COL] ?? null;

        /** @var string $name */
        $name = $data[RoleRepository::NAME_COL] ?? '';

        return new self(
            id: $id,
            name: $name,
        );
    }

    public function toArray(): array
    {
        return [
            RoleRepository::ID_COL => $this->id,
            RoleRepository::NAME_COL => $this->name,
        ];
    }
}

