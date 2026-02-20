<?php

namespace App\Model\Role\Facades;

use App;
use App\Model;
use App\Model\Permission\DTO\PermissionDTO;
use App\Model\Permission\Repo\PermissionRepository;
use App\Model\RepoConnections\PermissionRoleRepository;
use App\Model\Role\DTO\RoleDTO;
use Nette\Database\Explorer;

class RolePermissionFacade
{
    public function __construct(
        private readonly App\Model\Permission\Repo\PermissionRepository $permissionRepository,
        private readonly PermissionRoleRepository $permissionRoleRepository,
        private readonly Model\Permission\Mapper\PermissionMapper $permissionMapper,
        private readonly Explorer $database,
        private readonly Model\Permission\Facades\PermissionFacade $permissionFacade,
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


    /**
     * @param int $roleDTO
     * @param array<string,bool> $permissions
     * @return bool
     */
    public function updateRolePermissions(RoleDTO $roleDTO, array $permissions): bool {
        $this->database->beginTransaction();
        try {
            $this->permissionRoleRepository->getAllByX(PermissionRoleRepository::ROLE_ID_COL,$roleDTO->id)->delete();

            foreach ($permissions as $permission => $value) {
                if ($value) {
                    $this->permissionRoleRepository->insert([
                        'role_id' => $roleDTO->id,
                        'permission_id' => $this->permissionFacade
                            ->getDTOByX(PermissionRepository::NAME_COL, $permission)->id,
                    ]);
                }
            }
            $this->database->commit();
            return true;
        } catch (\Exception $exception) {
            $this->database->rollBack();
            throw $exception;
        }
    }
}