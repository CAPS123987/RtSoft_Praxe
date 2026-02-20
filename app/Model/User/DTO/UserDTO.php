<?php

namespace App\Model\User\DTO;

use App\Model\Generics\DTO\DTO;
use App\Model\Role\DTO\RoleDTO;
use Nette\Utils\DateTime;
use App\Model\User\Repo\UserRepository;

class UserDTO implements DTO
{
    private function __construct(
        public readonly ?int      $id,
        public readonly string   $name = '',
        public readonly int   $role = -1,
        public readonly string   $password = '',
        public readonly ?RoleDTO  $resolvedRole = null,
    ) {
    }

    public static function create(?int $id, string $name, int $role, string $password, ?RoleDTO $resolvedRole): self {
        return new self($id, $name, $role, $password, $resolvedRole);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data[UserRepository::ID_COL] ?? null,
            name: $data[UserRepository::NAME_COL] ?? '',
            role: $data[UserRepository::ROLE_COL] ?? -1,
            password: $data[UserRepository::PASSWORD_COL] ?? '',
            resolvedRole: $data["resolvedRole"] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            UserRepository::ID_COL => $this->id,
            UserRepository::NAME_COL => $this->name,
            UserRepository::ROLE_COL => $this->role,
            UserRepository::PASSWORD_COL => $this->password,
        ];
    }
}