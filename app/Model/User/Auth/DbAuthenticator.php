<?php

namespace App\Model\User\Auth;

use App\Model\User\Auth\Facades\RolePermissionFacade;
use App\Model\User\Facades\UserFacade;
use App\Model\User\Repo\UserRepository;
use Nette;
use Nette\Security\SimpleIdentity;

class DbAuthenticator implements Nette\Security\Authenticator
{
    public function __construct(
        private Nette\Database\Explorer $database,
        private Nette\Security\Passwords $passwords,
        private RolePermissionFacade $rolePermissionFacade,
        private UserFacade $userFacade,
    ) {
    }

    public function authenticate(string $username, string $password): BlogIdentity
    {
        try {
            $user = $this->userFacade->getDTOByX(UserRepository::NAME_COL, $username);
        } catch (\RuntimeException $e) {
            throw new Nette\Security\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $user->password)) {
            throw new Nette\Security\AuthenticationException('Invalid password.');
        }

        $permissions = $this->rolePermissionFacade->getRolePermissionsByRoleId($user->role);

        return new BlogIdentity(
            $user->id,
            $permissions,
            $user->role, // nebo pole více rolí
            ['name' => $user->name],
        );
    }

    public function isAllowed($role, $resource, $operation): bool
    {
        if ($role === 'admin') {
            return true;
        }
        if ($role === 'user' && $resource === 'article') {
            return true;
        }

        // ...

        return false;
    }
}