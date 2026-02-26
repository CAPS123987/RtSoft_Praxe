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
        public readonly ?DateTime $lastLogin = null,
    ) {
    }

    public static function create(?int $id, string $name, int $role, string $password, ?RoleDTO $resolvedRole, ?DateTime $lastLogin = null): self {
        return new self($id, $name, $role, $password, $resolvedRole, $lastLogin);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        /** @var int|null $id */
        $id = $data[UserRepository::ID_COL] ?? null;

        /** @var string $name */
        $name = $data[UserRepository::NAME_COL] ?? '';

        /** @var int $role */
        $role = $data[UserRepository::ROLE_COL] ?? -1;

        /** @var string $password */
        $password = $data[UserRepository::PASSWORD_COL] ?? '';

        /** @var RoleDTO|null $resolvedRole */
        $resolvedRole = $data["resolvedRole"] ?? null;

        $lastLoginRaw = $data[UserRepository::LAST_LOGIN_COL] ?? null;
        $lastLogin = $lastLoginRaw instanceof DateTime
            ? $lastLoginRaw
            : ($lastLoginRaw !== null ? DateTime::from($lastLoginRaw) : null);

        return new self(
            id: $id,
            name: $name,
            role: $role,
            password: $password,
            resolvedRole: $resolvedRole,
            lastLogin: $lastLogin,
        );
    }

    public function toArray(): array
    {
        return [
            UserRepository::ID_COL => $this->id,
            UserRepository::NAME_COL => $this->name,
            UserRepository::ROLE_COL => $this->role,
            UserRepository::PASSWORD_COL => $this->password,
            UserRepository::LAST_LOGIN_COL => $this->lastLogin,
        ];
    }
}