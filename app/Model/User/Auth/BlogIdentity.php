<?php

namespace App\Model\User\Auth;

use App\Model\Permission\DTO\PermissionDTO;
use Nette\Security\SimpleIdentity;

use Nette;
class BlogIdentity extends SimpleIdentity
{
    /**
     * @var array<PermissionDTO>
     */
    private array $permissions;

    /**
     * @param array<PermissionDTO> $permissions
     * @param iterable<string, mixed>|null $data
     */
    public function __construct(int|string $id, array $permissions, mixed $roles = null, ?iterable $data = null) {
        parent::__construct($id, $roles, $data);
        $this->permissions = $permissions;
    }

    /**
     * @return array<PermissionDTO>
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        foreach ($this->permissions as $perm) {
            if ($perm->name === $permission) {
                return true;
            }
        }
        return false;
    }
}