<?php

namespace App\Model\Role\Facades;

use App;
use App\Model\Role\DTO\RoleDTO;
use App\Model\Generics\Facades\DTOFacade;

/**
 * @extends DTOFacade<RoleDTO>
 */
final class RoleFacade extends DTOFacade
{
    private const array ADMIN_ROLES = ['admin', 'superAdmin'];

    public function __construct(
        App\Model\Role\Repo\RoleRepository $roleRepository,
        App\Model\Role\Mapper\RoleMapper   $roleMapper,
    ) {
        parent::__construct($roleRepository, $roleMapper);
    }

    /**
     * Returns role options filtered by permission.
     * Admin/superAdmin roles are hidden unless $canAddAdmin is true,
     * except when $keepRoleId matches (so the current user's role is always visible).
     *
     * @param bool $canAddAdmin Whether the current user has addAdmin permission
     * @param int|null $keepRoleId Role ID to always include (e.g. edited user's current role)
     * @return array<int, string> [roleId => roleName]
     */
    public function getFilteredRoleOptions(bool $canAddAdmin, ?int $keepRoleId = null): array
    {
        $options = [];
        foreach ($this->getAllDTO() as $role) {
            if (in_array($role->name, self::ADMIN_ROLES, true) && !$canAddAdmin) {
                if ($keepRoleId === null || $role->id !== $keepRoleId) {
                    continue;
                }
            }
            $options[$role->id] = $role->name;
        }
        return $options;
    }
}

