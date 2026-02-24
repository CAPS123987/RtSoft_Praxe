<?php

namespace App\Model\User\Facades;

use App\Model\Role\DTO\RoleDTO;
use App\Model\Role\Facades\RoleFacade;
use App\Model\User\DTO\UserDTO;

class UserRoleFacade
{
    public function __construct(
        private readonly RoleFacade $roleFacade
    ) {

    }

    public function resolveUserRole(UserDTO $user): RoleDTO
    {
        return $this->roleFacade->getDTOById($user->role);
    }

    /**
     * resolves the role for each user in the array and returns an array of users with resolved roles
     * @param array<UserDTO> $user
     * @return array<UserDTO>
     */
    public function resolveUsersRoles(array $user): array
    {
        $newUsers = [];
        foreach ($user as $u) {
            $newUsers[] = UserDTO::create(
                id: $u->id,
                name: $u->name,
                role: $u->role,
                password: $u->password,
                resolvedRole: $this->resolveUserRole($u)
            );
        }
        return $newUsers;
    }

}