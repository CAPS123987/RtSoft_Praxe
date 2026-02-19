<?php

namespace App\Model\User\Auth\Facades;

use App\Model\Permission\DTO\PermissionDTO;
use App\Model\RepoConnections\PermissionRoleRepository;
use Nette;
use App;
use App\Model;
class RolePermissionFacade
{
    public function __construct(
        private readonly App\Model\Permission\Repo\PermissionRepository $permissionRepository,
        private readonly PermissionRoleRepository $permissionRoleRepository,
        private readonly Model\Permission\Mapper\PermissionMapper $permissionMapper,
    )
    {
    }

    /**
     * @param int $roleId
     * @return array<PermissionDTO>
     */
    public function getRolePermissionsByRoleId(int $roleId): array
    {
        $relations = $this->permissionRoleRepository->getAllByX(
            PermissionRoleRepository::ROLE_ID_COL, strval($roleId));
        //get all permissions using the relations permission id without cycle
        $permissions = [];
        foreach ($relations as $relation) {
            $permission = $this->permissionRepository->getById($relation->{PermissionRoleRepository::PERMISSION_ID_COL});
            if ($permission) {
                $permissions[] = $this->permissionMapper->map($permission);
            }
        }
        return $permissions;
    }
}